import * as url from 'App/Modules/Url';


/**
 * @type {Intl.DateTimeFormatOptions}
 */
export const dateFormat = {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
};

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

export const PAGINATION_ITEMS_MAX = 50;

/**
 * @type {string}
 */
export const CURRENCY = '€';

export const UNIT_MEASURE = {
    /**
     * @param {string} unit
     * @param {boolean} plural
     * @returns {string}
     */
    translate: (unit, plural) => {
        if (typeof UNIT_MEASURE[unit] === 'undefined') {
            return unit;
        }

        if (unit !== UNIT_MEASURE.UNITS) {
            return UNIT_MEASURE[unit];
        }

        const locale = url.getLocale();

        if (locale === 'en') {
            return plural ? 'Units' : 'Unit';
        }

        return plural ? 'Unidades' : 'Unidad';
    },

    UNITS: 'UNITS',

    KG: 'Kg',
    G: 'g',
    CG: 'cg',

    M: 'm',
    DM: 'dm',
    CM: 'cm',
    MM: 'mm',

    L: 'l',
    DL: 'dl',
    CL: 'dl',
    ML: 'ml',
};


/**
 * @typedef {Object} ItemData
 * @class
 * @property {string} id
 * @property {string} name
 * @property {string} description
 * @property {string} image
 * @property {boolean} noImage
 * @property {string} createdOn
 * @property {ItemPriceData[]} itemsPrices
 */
/**
 * @typedef {Object} ItemPriceData
 * @property {string} id
 * @property {string} name
 * @property {string} description
 * @property {string} image
 * @property {number} price
 * @property {string} unit
 */

/**
 * @typedef {object} OrderData
 * @class
 * @property {string} id
 * @property {string} name
 * @property {string|null} description
 * @property {float} amount
 * @property {boolean} bought
 * @property {string} image
 * @property {string} createdOn
 * @property {OrderDataProduct} product
 * @property {OrderDataShop} shop
 * @property {OrderDataProductShop} productShop
 */

/**
 * @typedef {object} OrderDataProduct
 * @class
 * @property {string} id
 * @property {string} name
 * @property {string|null} description
 */

/**
 * @typedef {object} OrderDataShop
 * @class
 * @property {string|null} id
 * @property {string|null} name
 * @property {string|null} description
 */

/**
 * @typedef {object} OrderDataProductShop
 * @class
 * @property {float|null} price
 * @property {string|null} unit
 */


