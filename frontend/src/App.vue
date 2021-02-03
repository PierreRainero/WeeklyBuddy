<template>
    <el-container direction="vertical">
        <wb-header style="height: 6vh" />
        <el-container style="height: 94vh">
            <wb-menu />
            <el-container class="wb-main">
                <el-main>
                    <router-view />
                </el-main>
            </el-container>
        </el-container>
    </el-container>
</template>

<script>
import WbHeader from '@components/WbHeader.vue';
import WbMenu from '@components/WbMenu.vue';
import '~styles/common.scss';

export default {
    /**
     * @component
     * @name App
     * @description The main component of the application, it's composed of the current content, a naviguation menu and an header. It's provides all CSS need to render the application to every devices (mobile, tablet, computer screen).
     */
    name: 'App',
    components: { WbMenu, WbHeader },
    /**
     * @function
     * @description Initializes le store at the application launch
     */
    beforeMount() {
        this.$store.commit('init');
    },
};
</script>

<style lang="scss">
@import '~styles/constants.module.scss';

.wb-main {
    overflow-y: hidden;
    margin-left: -20px;
    z-index: 1;
    justify-content: center;

    background-color: $--color-background;
}

.wb-main > .el-main {
    padding: 0 2px;
    max-width: 100%;
}

@media (max-width: ($--breakpoint--tablet-vertical - 1)+'px') {
    .wb-main > .el-main {
        max-width: auto;
    }

    .wb-main > .el-main:first-child {
        margin-top: 4px;
    }
}

@media (min-width: $--breakpoint--tablet-vertical+'px') and (max-width: ($--breakpoint--tablet-horizontal - 1)+'px') {
    .wb-main > .el-main {
        max-width: 660px;
    }

    .wb-main > .el-main:first-child {
        margin-top: 8px;
    }
}

@media (min-width: $--breakpoint--tablet-horizontal+'px') and (max-width: ($--breakpoint--desktop - 1)+'px') {
    .wb-main > .el-main {
        max-width: 870px;
    }

    .wb-main > .el-main:first-child {
        margin-top: 12px;
    }
}

@media (min-width: $--breakpoint--desktop+'px') {
    .wb-main > .el-main {
        max-width: 1070px;
    }

    .wb-main > .el-main:first-child {
        margin-top: 16px;
    }
}
</style>
