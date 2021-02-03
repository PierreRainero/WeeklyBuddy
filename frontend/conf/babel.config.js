module.exports = {
    presets: [
        '@babel/preset-env',
        ['@vue/babel-preset-app', { modules: false }],
    ],
    /**
     * Babel pre-config for style files
     */
    plugins: [
        '@babel/plugin-proposal-class-properties',
        '@babel/plugin-syntax-dynamic-import',
        'babel-plugin-dynamic-import-node',
        ['component',
            {
                'libraryName': 'element-plus',
                'styleLibraryName': 'theme-chalk'
            },
        ],
    ],
    env: {
        test: {
            plugins: [
                '@babel/plugin-transform-runtime',
            ],
        },
    },
};