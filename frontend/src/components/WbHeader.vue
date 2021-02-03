<template>
    <el-header class="wb-header">
        <h1 class="wb-main-title" style="padding-left: 0.4rem">Weekly Buddy</h1>
        <wb-lang-selector />
    </el-header>
</template>

<script>
/**
 * @component
 * @name WbHeader
 * @description This component represents the head on the top of the application
 */
import WbLangSelector from '@components/WbLangSelector.vue';

export default {
    name: 'WbHeader',
    components: { WbLangSelector },
};
</script>

<style lang="scss">
@import '~styles/constants.module.scss';

.wb-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    background-color: $--color-primary;
    color: $--color-text-secondary;
}
</style>
