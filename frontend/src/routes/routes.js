import store from '../store/store';
const Login = () => import('@views/Login.vue');
const Register = () => import('@views/Register.vue');

/**
 * @function
 * @description Intercept a routing to check if the user is correctly log to accept the routing
 * @param {object} to Object indicating where the routing should go
 * @param {object} from Object indicating from where the routing is done
 * @param {function} next Function to route the user
 */
function requireToBeAuthentified(to, from, next) {
    if (to.name !== 'Login' && !store.getters.isAuthenticated) {
        store.commit('removeToken');
        next({ name: 'Login' });
    } else {
        next();
    }
}

/**
 * @function
 * @description Intercept a routing to check if the user isn't log to accept the routing
 * @param {object} to Object indicating where the routing should go
 * @param {object} from Object indicating from where the routing is done
 * @param {function} next Function to route the user
 */
function requireToBeNotAuthentified(to, from, next) {
    if (store.getters.isAuthenticated) {
        next({ path: '/' });
    } else {
        next();
    }
}

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: Login,
        beforeEnter: requireToBeNotAuthentified,
    },
    {
        path: '/register',
        name: 'Register',
        component: Register,
        beforeEnter: requireToBeNotAuthentified,
    },
];

export default routes;
