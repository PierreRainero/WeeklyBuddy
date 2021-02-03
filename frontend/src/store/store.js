import { createStore } from 'vuex';
import jwt_decode from 'jwt-decode';

const store = createStore({
    state() {
        return { token: null };
    },
    mutations: {
        init(state) {
            state.token = localStorage.getItem('wb-token');
        },
        addToken(state, token) {
            state.token = token;
            localStorage.setItem('wb-token', token);
        },
        removeToken(state) {
            state.token = null;
            localStorage.removeItem('wb-token');
        },
    },
    getters: {
        isAuthenticated: (state) => {
            if (state.token === null || state.token === undefined) {
                return false;
            }
            const decryptedToken = jwt_decode(state.token);
            if (!decryptedToken.hasOwnProperty('exp')) {
                return false;
            }
            const expirationDate = new Date(0);
            expirationDate.setUTCSeconds(decryptedToken.exp);
            return new Date() < expirationDate;
        },
    },
});

export default store;
