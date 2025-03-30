/**
 * @type {Intl.DateTimeFormatOptions}
 */
export const dateTimeFormat = {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: false
};

/**
 * @type {Intl.DateTimeFormatOptions}
 */
export const dateFormat = {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
};


/**
 * @typedef {object} RecipeData
 * @property {string} userOwnerName
 * @property {string} id
 * @property {string} name
 * @property {string} description
 * @property {string} preparation_time
 * @property {string} category
 * @property {boolean} public
 * @property {array} ingredients
 * @property {array} steps
 * @property {string} image
 * @property {number} rating
 * @property {string} createdOn
 */
