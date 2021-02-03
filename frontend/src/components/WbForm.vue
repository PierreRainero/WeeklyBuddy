<template>
    <div class="wb-form-wrapper">
        <h3 class="wb-form-title" v-if="title">{{ title }}</h3>
        <el-form
            v-bind="$attrs"
            :model="form"
            :rules="rulesWithBackend"
            label-position="top"
            size="small"
            class="wb-form"
            ref="formElement"
        >
            <slot></slot>
            <div class="wb-form-actions">
                <slot name="actions">
                    <el-button type="primary" :icon="`fas ${actionIcon}`" round @click="submitForm()">
                        {{ $t(actionTitleKey) }}
                    </el-button>
                </slot>
            </div>
        </el-form>
    </div>
</template>

<script>
/**
 * @component
 * @name WbForm
 * @description This component represents a form for the application, it centralizes sending and success/errors management
 */
export default {
    name: 'WbForm',
    props: {
        /**
         * @property {object} form The model manipulates by the form
         */
        form: {
            type: Object,
            required: true,
        },
        /**
         * @property {function} action The action to trigger when a form submit is fired (required the form to be valid)
         */
        action: {
            type: Function,
            required: true,
        },
        /**
         * @property {string} title The title of the form to display at the top
         */
        title: {
            type: String,
        },
        /**
         * @property {object} rules The validation rules to apply, they are on the following format : { fieldName : [ {validator: myValidator, message: myErrorMessage} ]}
         */
        rules: {
            type: Object,
        },
        /**
         * @property {string} [successMessageKey='successAction'] The translation key for the success notifier message
         */
        successMessageKey: {
            type: String,
            default: 'successAction',
        },
        /**
         * @property {function} onSuccess The function to trigger if the submission is a success, nothing happens by default
         */
        onSuccess: {
            type: Function,
            default: () => {},
        },
        /**
         * @property {function} onFailure The function to trigger if the submission is a failure, nothing happens by default
         */
        onFailure: {
            type: Function,
            default: () => {},
        },
        /**
         * @property {string} [actionTitleKey='common.send'] The translation key for the sending button
         */
        actionTitleKey: {
            type: String,
            default: 'common.send',
        },
        /**
         * @property {string} [actionIcon='fa-check'] The CSS class for the icon in the sending button
         */
        actionIcon: {
            type: String,
            default: 'fa-check',
        },
    },
    /**
     * @description Data directly manipulate by the component
     * @returns {{fieldsInErrorFromBackend: object, shadowForm: object}} "fieldsInErrorFromBackend" stores the errors from the backend validation, "shadowForm" is a copy of the current form used to remove errors from backend when user changes a concerned field
     */
    data() {
        return {
            fieldsInErrorFromBackend: {},
            shadowForm: Object.assign({}, this.form),
        };
    },
    computed: {
        /**
         * @description Creates the rules for the current form according to given rules in props and adds a validator to accept errors from backend
         * @returns {object}
         */
        rulesWithBackend() {
            const generatedRules = {};
            for (const fieldName in this.rules) {
                generatedRules[fieldName] = this.copyRules(this.rules[fieldName]);
                generatedRules[fieldName].push({
                    validator: (rule, value, callback) => {
                        if (this.fieldsInErrorFromBackend.hasOwnProperty(fieldName)) {
                            callback(
                                `${this.$t(`common.${fieldName}.title`)} ${this.$t(
                                    `validation.backend.${this.fieldsInErrorFromBackend[fieldName]}`
                                )}`
                            );
                        }
                        callback();
                    },
                });
            }
            return generatedRules;
        },
    },
    watch: {
        /**
         * @description Watch the manipulated form to remove errors from backend if a concerned field is touched and updates the "shadowForm"
         */
        form: {
            handler: function (newVal) {
                for (const field in newVal) {
                    if (this.shadowForm.hasOwnProperty(field) && this.fieldsInErrorFromBackend.hasOwnProperty(field)) {
                        if (this.shadowForm[field] !== newVal[field]) {
                            delete this.fieldsInErrorFromBackend[field];
                        }
                    }
                }
                this.shadowForm = Object.assign({}, newVal);
            },
            deep: true,
        },
    },
    methods: {
        /**
         * @function
         * @description "Deep" copy given rules without linking the result to parameter
         * @param {object} rules Rules to copy
         * @returns {array} Copy of given rules
         */
        copyRules(rules) {
            const copiedRules = [];
            for (const rule of rules) {
                copiedRules.push(this.copyRule(rule));
            }
            return copiedRules;
        },
        /**
         * @function
         * @description "Deep" copy given rule without linking the result to parameter (it only change the object reference, subproperty as "validator" for example are keeped)
         * @param {object} rule Rule to copy
         * @returns {object} Copy of a given rule
         */
        copyRule(rule) {
            const copiedRule = {};
            for (const prop in rule) {
                copiedRule[prop] = rule[prop];
            }
            return copiedRule;
        },
        /**
         * @function
         * @description Submits the form if it's valid
         */
        submitForm() {
            this.$refs.formElement.validate((valid) => {
                if (valid) {
                    this.action()
                        .then((response) => {
                            this.onSuccess(response.data);
                            this.displaySuccessNotifier();
                        })
                        .catch((error) => {
                            this.onFailure(error);
                            this.applyBackendErrorsToFields(error.response.data);
                            this.displayErrorNotifier();
                        });
                }
            });
        },
        /**
         * @function
         * @description Displays a toast to indicates the submission ends with success
         */
        displaySuccessNotifier() {
            this.$notify({
                title: this.$t('common.success'),
                type: 'success',
                message: this.$t(this.successMessageKey),
                duration: 5000,
            });
        },
        /**
         * @function
         * @description Displays a toast to indicates the submission fails
         */
        displayErrorNotifier() {
            this.$notify({
                title: this.$t('common.error'),
                type: 'error',
                message: this.$t('errors.actionFailed'),
                duration: 0,
            });
        },
        /**
         * @function
         * @description Applies the errors from backend to the concerned fields
         * @param {object} data Content returned by the backend response
         */
        applyBackendErrorsToFields(data) {
            if (data.hasOwnProperty('fieldsInError')) {
                this.fieldsInErrorFromBackend = data.fieldsInError;
                this.$refs.formElement.validate(() => {});
            }
        },
    },
};
</script>

<style lang="scss">
@import '~styles/constants.module.scss';

.wb-form-wrapper {
    position: relative;
    margin-top: 10px;
    padding: 1rem 2% 1rem;

    border: 2px solid $--color-primary;
    border-radius: 10px;
}

.wb-form-wrapper .wb-form-title {
    position: absolute;
    top: -0.75rem;
    left: 1.1rem;
    padding: 0 4px;
    margin: 0;

    background-color: $--color-background;
}

.wb-form-wrapper .wb-form-actions {
    position: absolute;
    bottom: -1.5rem;
    right: 1rem;
}

.wb-form.el-form .el-form-item--small.el-form-item {
    margin-bottom: 1.7rem;
}

.wb-form.el-form .el-form-item__label {
    padding-bottom: 0;
}

.wb-form.el-form .el-form-item__error {
    display: block;
    text-overflow: ellipsis;
    word-wrap: break-word;
    overflow: hidden;
    max-height: 2em;
    line-height: 1em;
}
</style>
