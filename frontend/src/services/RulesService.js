const lowercase = /[a-z]/;
const uppercase = /[A-Z]/;
const digit = /[0-9]/;
const specialChar = /[!#$%&()*+,-./:;<=>?@[\\\]^_{}~]/;

/**
 * @function
 * @description Rule to valid a password (1 lowercase, 1 uppercase, 1 digit, 1 special char)
 * @param {object} rule Reference to the rule object
 * @param {any} value The value to valid
 * @param {function} callback The function to end the validation (empty if valid with an error otherwise)
 */
const passwordRule = (rule, value, callback) => {
    if (lowercase.test(value) && uppercase.test(value) && digit.test(value) && specialChar.test(value)) {
        callback();
    }
    callback(new Error(rule.message));
};

export { passwordRule };
