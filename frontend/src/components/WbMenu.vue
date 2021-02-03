<template>
    <div class="wb-menu-wrapper">
        <el-menu
            mode="vertical"
            :collapse="isCollapsed"
            class="wb-menu"
            :text-color="styles.textColor"
            :background-color="styles.darkColor"
            :active-text-color="styles.activeColor"
            :collapse-transition="false"
        >
            <div v-if="$store.getters.isAuthenticated">
                <el-menu-item index="1" @click="logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <template #title>
                        {{ $t('logout.title') }}
                    </template>
                </el-menu-item>
            </div>
            <div v-else>
                <router-link to="/login">
                    <el-menu-item index="1">
                        <i class="fas fa-sign-in-alt"></i>
                        <template #title>
                            {{ $t('login.title') }}
                        </template>
                    </el-menu-item>
                </router-link>
                <router-link to="/register">
                    <el-menu-item index="2">
                        <i class="fas fa-user-plus"></i>
                        <template #title>
                            {{ $t('register.title') }}
                        </template>
                    </el-menu-item>
                </router-link>
            </div>
        </el-menu>
        <div class="menu-collapser">
            <i :class="`fas ${collapserIcon} clickable`" v-on:click="collapseMenu"></i>
            <span v-if="!mobileScreen" class="clickable" v-on:click="collapseMenu">Menu</span>
            <i v-if="!mobileScreen" :class="`fas ${collapserIcon} clickable`" v-on:click="collapseMenu"></i>
        </div>
    </div>
</template>

<script>
/**
 * @component
 * @name WbMenu
 * @description This component represents the lateral navigation menu of the application
 */
import colors from '~styles/colors.module.scss';
import { isOnMobile, isAtLeastOnHorizontalTablet } from '@services/ScreenService';

export default {
    name: 'WbMenu',
    /**
     * @description Data directly manipulate by the component
     * @returns {{isCollapsed: boolean, styles: object, boolean}} "isCollapsed" indicates if the menu is expanded or collapsed, "styles" is an object to use sass variables in the template, "mobileScreen" indicates if the application is currently display on a mobile device
     */
    data() {
        return {
            isCollapsed: !isAtLeastOnHorizontalTablet(window.innerWidth),
            styles: {
                activeColor: colors.primaryColor,
                darkColor: colors.darkColor,
                textColor: colors.textSecondaryColor,
            },
            mobileScreen: isOnMobile(window.innerWidth),
        };
    },
    /**
     * @function
     * @description Adds a listener to trigger the function "windowResized" when the device is resized
     */
    created() {
        window.addEventListener('resize', this.windowResized);
    },
    /**
     * @function
     * @description Removes the listener on the device resize when the component is unmounted
     */
    unmounted() {
        window.removeEventListener('resize', this.windowResized);
    },
    computed: {
        /**
         * @description Computes what icon should be display according to the fact the menu is collapsed or not
         * @returns {string} CSS class to the icon to use
         */
        collapserIcon: function () {
            return `fa-caret-${this.isCollapsed ? 'right' : 'left'}`;
        },
    },
    methods: {
        /**
         * @function
         * @description Collapses or expands the menu
         */
        collapseMenu() {
            this.isCollapsed = !this.isCollapsed;
        },
        /**
         * @function
         * @description Re-computes the data "mobileScreen" according to the current device size
         */
        windowResized() {
            this.mobileScreen = isOnMobile(window.innerWidth);
        },
        /**
         * @function
         * @description Logouts the actual connected user
         */
        logout() {
            this.$store.commit('removeToken');
        },
    },
};
</script>

<style lang="scss">
@import '~styles/constants.module.scss';

.wb-menu-wrapper {
    display: flex;
}

.menu-collapser {
    display: grid;
    grid-template-rows: 3fr 0.5fr 3fr;
    width: 20px;
    z-index: 2;

    color: #fff;
}

@media (max-width: ($--breakpoint--tablet-vertical - 1)+'px') {
    .menu-collapser {
        grid-template-rows: 1fr;
    }
}

.menu-collapser *:first-child {
    align-self: end;
    border-top: solid;
    border-right: solid;
    border-top-right-radius: 25%;
}

.menu-collapser *:last-child {
    align-self: start;
    border-bottom: solid;
    border-right: solid;
    border-bottom-right-radius: 25%;
}

.menu-collapser *:only-child {
    align-self: center;
}

.menu-collapser span {
    writing-mode: sideways-lr;
    border-right: solid;
}

.menu-collapser * {
    border-color: $--color-primary;
    background-color: $--color-primary;
    border-width: 1px;
    text-align: center;
}

.el-menu.wb-menu {
    border-color: $--color-primary;
}
.wb-menu a {
    text-decoration: none;
}
</style>
