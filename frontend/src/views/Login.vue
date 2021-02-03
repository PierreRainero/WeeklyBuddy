<template>
    <wb-form
        :form="form"
        :rules="rules"
        :title="$t('login.title')"
        actionTitleKey="login.title"
        actionIcon="fa-sign-in-alt"
        :action="connectUser"
        :onSuccess="storeToken"
        successMessageKey="login.success"
    >
        <el-form-item :label="$t('common.email.title')" prop="email">
            <el-input v-model="form.email" :placeholder="$t('common.email.placeholder')" />
        </el-form-item>
        <el-form-item :label="$t('common.password.title')" prop="password">
            <el-input v-model="form.password" show-password :placeholder="$t('common.password.placeholder')" />
        </el-form-item>
    </wb-form>
</template>

<script>
import WbForm from '@components/WbForm.vue';
import { connect } from '@services/UsersService';

/**
 * @component
 * @name Login
 * @description This component represents the login page
 */
export default {
    name: 'Login',
    components: { WbForm },
    /**
     * @description Data directly manipulate by the component
     * @returns {{form: {email: string, shadowForm: password}}} The login form (composed of the user email and password)
     */
    data() {
        return {
            form: {
                email: '',
                password: '',
            },
        };
    },
    computed: {
        /**
         * @description The rules to reach to accept the login form. Email and password are required.
         * @returns {object}
         */
        rules: function () {
            return {
                email: [
                    {
                        required: true,
                        message: this.$t('validation.required'),
                    },
                ],
                password: [
                    {
                        required: true,
                        message: this.$t('validation.required'),
                    },
                ],
            };
        },
    },
    methods: {
        /**
         * @function
         * @description Calls a connection through the UsersService
         * @returns {Promise}
         */
        connectUser() {
            return connect(this.form.email, this.form.password);
        },
        /**
         * @function
         * @description Adds the authentication token to the user store
         * @param {object} data Content of the connection response from the backend
         */
        storeToken(data) {
            if (data.hasOwnProperty('token')) {
                this.$store.commit('addToken', data.token);
                this.$router.push({ path: '/' });
            }
        },
    },
};
</script>
