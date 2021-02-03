import axios from 'axios';
import { getBaseUrl } from './UrlService';

/**
 * @function
 * @description Sends a connexion request to the backend for a given user
 * @param {string} email Email to identify the user
 * @param {string} password Corresponding password
 * @returns {Promise}
 */
const connect = (email, password) => {
    return axios.post(`${getBaseUrl()}/users/connection`, {
        email: email,
        password: password,
    });
};

/**
 * @function
 * @description Sends a register request to the backend to add a new user
 * @param {string} email Email to identify the user
 * @param {string} password Corresponding password
 * @param {string} lang Language of the new user
 * @returns {Promise}
 */
const register = (email, password, lang) => {
    return axios.post(`${getBaseUrl()}/users`, {
        email: email,
        password: password,
        lang: lang,
    });
};

export { connect, register };
