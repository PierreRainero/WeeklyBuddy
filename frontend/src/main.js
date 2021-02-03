import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { createI18n } from 'vue-i18n';
import App from './App.vue';
import store from './store/store';
import routes from './routes/routes';
import messages from './i18n/messages';
import ElementPlus from 'element-plus';
import '~styles/constants.module.scss';

const router = createRouter({ history: createWebHistory(), routes });

const i18n = createI18n({
    locale: navigator.language || navigator.userLanguage,
    fallbackLocale: 'en',
    messages,
});

createApp(App).use(store).use(router).use(i18n).use(ElementPlus).mount('#app');
