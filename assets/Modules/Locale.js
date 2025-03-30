import * as url  from 'App/Modules/Url';
import * as config from 'App/Config';

/**
 * @param {string} date
 *
 * @returns {string}
 */
export function formatDateToLocale(date) {
    return new Date(date).toLocaleDateString(
        getLocaleItl(url.getLocale()),
        config.dateFormat
    );
}

/**
 * @param {string} locale
 *
 * @returns {string}
 */
function getLocaleItl(locale) {
    const localDictionary = {
        en: 'en-US',
        es: 'es-ES',
        default: 'en-US'
    };

    if (typeof localDictionary[locale] === 'undefined') {
        return localDictionary.default;
    }

    return localDictionary[locale];
}