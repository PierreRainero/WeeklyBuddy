/**
 * @function
 * @description Retrieves the base url to use in services according to the current environment
 * @returns {string} Base url to use
 */
const getBaseUrl = () => {
    return process.env.NODE_ENV === 'development'
        ? 'http://localhost:8080/weeklybuddy/backend'
        : 'https://weeklybuddy-api.pierre-rainero.fr';
};

export { getBaseUrl };
