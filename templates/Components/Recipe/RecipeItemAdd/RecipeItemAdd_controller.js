import { Controller } from "@hotwired/stimulus";
import TextAreaFallback from "App/Modules/TextArea/TextAreaFallback";
import * as Event from "App/Modules/Event";

export default class RecipeItemAdd extends Controller {

    /**
     * @type {HTMLTemplateElement}
     */
    #itemTemplate;

    /**
     * @type {HTMLDivElement}
     */
    #itemsContainer;

    /** @type {HTMLLegendElement} */
    #componentTitle;

    /**
     * @type {HTMLElement[]}
     */
    #items = [];

    connect() {
        this.#itemTemplate = this.element.querySelector('[data-js-item-template]');
        this.#itemsContainer = this.element.querySelector('[data-js-items-container]');
        this.#componentTitle = this.element.querySelector('[data-js-component-title]');

        Event.addEventListenerDelegate({
            element: this.element,
            eventName: 'input',
            elementDelegateSelector: '[data-js-input-input],[data-js-input-textarea]',
            callbackListener: this.#setFormControlValidity.bind(this),
            eventOptions: {}
        });
    }

    disconnect() {
        Event.removeEventListenerDelegate(this.element, 'input', this.#setFormControlValidity);
    }

    #setFormControlValidity() {
        const isValid = this.isValid();
        this.setIsValidStyles(isValid);
    }

    itemAdd() {
        const itemTemplate = /** @type {HTMLElement} */ (this.#itemTemplate.content.cloneNode(true));
        const textArea =/** @type {HTMLTextAreaElement|null} */ (itemTemplate.querySelector('[data-js-input-textarea]'));

        this.#itemsContainer.append(itemTemplate);
        this.#items.push(/** @type {HTMLElement} */(this.#itemsContainer.lastElementChild));

        if (textArea !== null) {
            new TextAreaFallback(textArea);
        }
    }

    /**
     * @param {MouseEvent} event
     */
    itemRemove(event) {
        if (!(event.target instanceof Element)) {
            return;
        }

        const itemRemoved = event.target.closest('[data-js-item]');
        const indexItemToRemove = this.#items.findIndex((item) => item.isEqualNode(itemRemoved));

        this.#items.splice(indexItemToRemove, 1);
        this.#itemsContainer.removeChild(itemRemoved);
    }

    /**
     * @param {HTMLElement} item
     * @returns {HTMLInputElement|HTMLTextAreaElement}
     */
    #getInputTag(item) {
        const input =/** @type {HTMLInputElement|null} */ (item.querySelector('[data-js-input-input]'));
        const textArea =/** @type {HTMLTextAreaElement|null} */ (item.querySelector('[data-js-input-textarea]'));

        return input ?? textArea;
    }

    /**
     * @returns {boolean}
     */
    hasItems() {
        return this.#items.length > 0;
    }

    /**
     * @returns {boolean}
     */
    hasItemsWithText() {
        if (!this.hasItems()) {
            return false;
        }

        for (const item of this.#items) {
            const inputTag = this.#getInputTag(item);

            if (inputTag === null) {
                return false;
            }

            if (inputTag.value.trim() === '') {
                return false;
            }

        }

        return true;
    }

    /**
     * @param {boolean} valid
     */
    setIsValidStyles(valid) {
        if (valid) {
            this.#componentTitle.classList.remove('is-invalid');

            return;
        }

        this.#componentTitle.classList.add('is-invalid');
    }

    /**
     * @returns {boolean}
     */
    isValid() {
        let isValid = true;

        this.#items.forEach((/** @type {HTMLInputElement|HTMLTextAreaElement} */item) => {
            const inputTag = this.#getInputTag(item);

            if (!inputTag.checkValidity()) {
                isValid = false;
            }
        });

        return isValid;
    }

    #clear() {
        this.#itemsContainer.innerHTML = '';
        this.setIsValidStyles(true);
    }

    handleMessageClear() {
        this.#clear();
    }
}