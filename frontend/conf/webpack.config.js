const path = require('path');
const glob = require('glob');
const CopyPlugin = require('copy-webpack-plugin');
const HtmlWebPackPlugin = require('html-webpack-plugin');
const CompressionPlugin = require('compression-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const PurgecssPlugin = require('purgecss-webpack-plugin');
const FontminPlugin = require('fontmin-webpack');
const { VueLoaderPlugin } = require('vue-loader');
const aliases = require('./aliases.json');

/**
 * Object to centralize all paths used for compilation
 */
const PATHS = {
    relative: {
        projectRoot: '..',
        confFolder: '.',
    },
    absolute: {
        projectRoot: path.join(__dirname, '..'),
        src: path.join(__dirname, '../src'),
        aliases: {},
    },
}
for (let aliase in aliases) {
    PATHS.absolute.aliases[aliase] = path.resolve(__dirname, `${PATHS.relative.projectRoot}/${aliases[aliase]}`);
}

module.exports = (env, options) => {
    /**
     * Only delete original assets on production mode (dev mode can't deal with gz files)
     */
    const deletingAssets = options.mode === 'production';

    return {
        /**
         * Entry and output points
         */
        entry: {
            main: `${PATHS.absolute.src}/main.js`,
        },
        output: {
            filename: '[name].[contenthash].js',
            path: path.resolve(__dirname, `${PATHS.relative.projectRoot}/dist`),
            publicPath: '/',
        },
        // =======================================
        /**
         * Rules to resolve aliases in imports
         */
        resolve: {
            extensions: ['*', '.js', '.vue'],
            alias: PATHS.absolute.aliases,
        },
        // =======================================
        /**
         * Loaders
         */
        module: {
            rules: [
                {
                    test: /^(?!.*\.test\.js$).*\.js$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                            configFile: path.join(__dirname, `${PATHS.relative.confFolder}/babel.config.js`),
                        },
                    },
                },
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {
                        loaders: {
                            scss: ['vue-style-loader', 'css-loader', 'sass-loader'],
                            js: 'babel-loader',
                        },
                    },
                },
                {
                    test: /\.css$/,
                    use: [
                        'style-loader',
                        'css-loader',
                        {
                            loader: 'postcss-loader',
                            options: {
                                postcssOptions: {
                                    config: path.join(__dirname, `${PATHS.relative.confFolder}/`),
                                },
                            },
                        },
                    ],
                },
                {
                    test: /\.scss$/,
                    use: [
                        'style-loader',
                        'css-loader',
                        {
                            loader: 'postcss-loader',
                            options: {
                                postcssOptions: {
                                    config: path.join(__dirname, `${PATHS.relative.confFolder}/`),
                                },
                            },
                        },
                        {
                            loader: 'sass-loader',
                        },
                    ]
                },
                {
                    test: /\.(png|jpg|gif)$/,
                    use: [
                        {
                            loader: 'file-loader',
                            options: {
                                outputPath: 'imgs/',
                            },
                        },
                    ],
                },
                {
                    test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
                    use: [{
                        loader: 'file-loader',
                        options: {
                            name: '[name].[ext]',
                            outputPath: 'fonts/',
                        },
                    }],
                },
                {
                    test: /\.html$/,
                    use: [
                        {
                            loader: 'html-loader',
                        },
                    ],
                },
            ],
            // =======================================
        },
        /**
         * Deploy dev server on port 3001 with routing available
         */
        devServer: {
            inline: true,
            historyApiFallback: true,
            hot: true,
            port: 3001,
        },
        // =======================================
        plugins: [
            /**
             * Vue loader for webpack
             */
            new VueLoaderPlugin(),
            /**
             * Copy all files from public folder to static website folder
             */
            new CopyPlugin({
                patterns: [{ from: `${PATHS.absolute.projectRoot}/public`, to: '.' }],
            }),
            /**
             * Generate html entry point from template
             */
            new HtmlWebPackPlugin({
                template: `${PATHS.absolute.src}/index.html`,
                filename: './index.html',
                inject: true,
            }),
            /**
             * Remove unused css
             */
            new PurgecssPlugin({
                paths: glob.sync(`${PATHS.absolute.src}/**/*`, { nodir: true }),
            }),
            /**
             * Minify fonts files
             */
            new FontminPlugin({
                autodetect: true,
            }),
            /**
             * Compress files to gz to reduce app size
             */
            new CompressionPlugin({
                test: /\.(css|js|svg|ttf|eot|woff|woff2|json)$/,
                deleteOriginalAssets: deletingAssets,
            }),
        ],
        optimization: {
            /**
             * Reduce files size by removing unnecessary lines
             */
            minimize: true,
            minimizer: [
                new TerserPlugin({
                    test: /\.js$/,
                    exclude: /node_modules/,
                    terserOptions: {
                        compress: {},
                        mangle: true,
                        sourceMap: true,
                    },
                }),
            ],
            /**
             * Find segments of the module graph which can be safely concatenated into a single module
             */
            concatenateModules: true,
            /**
             * Create a single runtime bundle for all chunks
             */
            runtimeChunk: 'single',
            /**
             * Split vendors (each npm packages)
             */
            splitChunks: {
                chunks: 'all',
                maxInitialRequests: Infinity,
                minSize: 0,
                cacheGroups: {
                    vendor: {
                        test: /[\\/]node_modules[\\/]/,
                        name(module) {
                            const packageName = module.context.match(/[\\/]node_modules[\\/](.*?)([\\/]|$)/)[1];
                            return `npm.${packageName.replace('@', '')}`;
                        },
                    },
                },
            },
        },
    };
};