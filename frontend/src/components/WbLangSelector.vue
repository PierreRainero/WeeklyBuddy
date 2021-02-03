<template>
    <el-select v-model="$i18n.locale" class="wb-lang-selector">
        <el-option v-for="locale in options" :key="locale.value" :label="locale.label" :value="locale.value">
            <span>{{ locale.label }}</span>
        </el-option>
    </el-select>
</template>

<script>
/**
 * @component
 * @name WbLangSelector
 * @description This component represents a combobox to switch the current language of the application
 */
export default {
    name: 'WbLangSelector',
    /**
     * @description Data directly manipulate by the component
     * @returns {{options: [{value: string, label: string}]}} Available languages
     */
    data() {
        const localesOptions = [];
        for (const locale of this.$i18n.availableLocales) {
            localesOptions.push({
                value: locale,
                label: this.formatLocale(locale),
            });
        }
        return {
            options: localesOptions,
        };
    },
    methods: {
        /**
         * @function
         * @description Formats a language to display it to the user
         * @param {string} locale Language to format
         * @returns {string} The formatted language
         */
        formatLocale: (locale) => {
            return locale.split('-')[0].toUpperCase();
        },
    },
};
</script>

<style>
.el-select.wb-lang-selector {
    width: 75px;
}

.el-select.wb-lang-selector .el-input__inner {
    height: 34px;
}

.el-select.wb-lang-selector .el-input__suffix {
    top: 5px;
}
</style>
