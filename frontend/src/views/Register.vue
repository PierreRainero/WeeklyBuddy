<template>
    <wb-form
        :form="form"
        :rules="rules"
        :title="$t('register.title')"
        actionTitleKey="register.title"
        actionIcon="fa-user-plus"
        :action="registerUser"
        successMessageKey="register.success"
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
import { passwordRule } from '@services/RulesService';
import { register } from '@services/UsersService';

export default {
    /**
     * @component
     * @name Register
     * @description This component represents the register page
     */
    name: 'Register',
    components: { WbForm },
    /**
     * @description Data directly manipulate by the component
     * @returns {{form: {email: string, shadowForm: password}}} The register form (composed of the user email and password)
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
         * @description The rules to reach to accept the register form.
         * Email is required and it should have an email format.
         * Password is required, it should be 8 characters long at least and contain 1 lowercase, 1 uppercase, 1 digit, 1 special char
         * @returns {object}
         */
        rules: function () {
            return {
                email: [
                    {
                        type: 'email',
                        message: this.$t('validation.email'),
                    },
                    {
                        required: true,
                        message: this.$t('validation.required'),
                    },
                ],
                password: [
                    {
                        min: 8,
                        message: this.$t('validation.min', { value: 8 }),
                    },
                    {
                        validator: passwordRule,
                        message: this.$t('validation.password'),
                    },
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
         * @description Calls an user registration through the UsersService
         * @returns {Promise}
         */
        registerUser() {
            return register(this.form.email, this.form.password, this.$i18n.locale);
        },
    },
};
</script>
