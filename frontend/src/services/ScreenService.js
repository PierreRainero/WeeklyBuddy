import constants from '~styles/constants.module.scss';

/**
 * @function
 * @description Determines if the provided size is corresponding to a mobile device
 * @param {number} size Device width
 * @returns {boolean}
 */
const isOnMobile = (size) => {
    return size < constants.tabletVertical;
};

/**
 * @function
 * @description Determines if the provided size is equal or bigger a tablet at the horizontal
 * @param {number} size Device width
 * @returns {boolean}
 */
const isAtLeastOnHorizontalTablet = (size) => {
    return size >= constants.tabletHorizontal;
};

export { isOnMobile, isAtLeastOnHorizontalTablet };
