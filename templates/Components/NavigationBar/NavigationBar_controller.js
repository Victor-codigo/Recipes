import { Controller } from '@hotwired/stimulus';
import * as themeManager from 'App/Modules/Theme';
import * as event from 'App/Modules/Event'

export default class extends Controller {

    connect() {
        event.addEventListenerDelegate({
            element: this.element,
            elementDelegateSelector: '[data-js-theme-button]',
            eventName: 'click',
            callbackListener: this.#toggleDarkMode.bind(this),
            eventOptions: {}
        });

        this.#setTheme(themeManager.getStoredTheme());
    }

    disconnect() {
        event.removeEventListenerDelegate(this.element, 'click', this.#toggleDarkMode);
    }

    #toggleDarkMode() {
        let theme = themeManager.THEMES.LIGHT;

        if (document.documentElement.dataset.bsTheme === themeManager.THEMES.LIGHT) {
            theme = themeManager.THEMES.DARK;
        }

        this.#setTheme(theme);
        themeManager.setStoredTheme(theme);
    }

    /**
     * @param {string} theme
     */
    #setTheme(theme) {
        /** @type {HTMLButtonElement[]} themeLight */
        const themeLightIconTags = document.querySelectorAll('[data-js-theme-light]');
        /** @type {HTMLButtonElement[]} themeDarkIconTag */
        const themeDarkIconTags = document.querySelectorAll('[data-js-theme-dark]');
        /** @type {HTMLButtonElement[]} themeDark */
        const themeAutoIconTags = document.querySelectorAll('[data-js-theme-auto]');

        for (let index in Array.from(themeAutoIconTags)) {
            themeManager.setTheme(theme, themeAutoIconTags[index], themeLightIconTags[index], themeDarkIconTags[index]);
        }
    }
}