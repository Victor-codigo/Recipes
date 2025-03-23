import { Controller } from '@hotwired/stimulus';
export default class extends Controller {


    /**
     * @type {HTMLFormElement}
     */
    searchBarFormTag;

    /**
     * @type {HTMLInputElement}
     */
    valueTag;

    /**
     * @type {HTMLInputElement}
     */
    fieldFilterTag;

    /**
     * @type {HTMLInputElement}
     */
    nameFilterTag;

    /**
     * @type {HTMLSelectElement}
     */
    categoryFilterTag;

    connect() {
        this.searchBarFormTag = this.element.querySelector('[data-js-searchbar-form]');
        this.valueTag = this.element.querySelector('[data-js-value]');
        this.fieldFilterTag = this.element.querySelector('[data-js-section-filter]');
        this.nameFilterTag = this.element.querySelector('[data-js-name-filter]');
        this.categoryFilterTag = this.element.querySelector('[data-js-category-filter]');

        this.#onFieldFilterChange();

        this.searchBarFormTag.addEventListener('submit', this.#onSubmitHandler.bind(this));
        this.fieldFilterTag.addEventListener('change', this.#onFieldFilterChange.bind(this));
    }

    disconnect() {
        this.searchBarFormTag.removeEventListener('submit', this.#onSubmitHandler);
    }

    /**
     * @param {boolean} visible
     */
    #showRecipeCategories(visible) {
        if(visible) {
            this.categoryFilterTag.closest('label').removeAttribute('hidden');
            this.valueTag.closest('label').setAttribute('hidden','hidden');
            this.nameFilterTag.closest('label').setAttribute('hidden','hidden');

            return;
        }

        this.valueTag.closest('label').removeAttribute('hidden');
        this.nameFilterTag.closest('label').removeAttribute('hidden');
        this.categoryFilterTag.closest('label').setAttribute('hidden','hidden');
    }

    #onFieldFilterChange() {
        if(this.fieldFilterTag.value.toLocaleLowerCase()==='category') {
            this.#showRecipeCategories(true);

            return;
        }

        this.#showRecipeCategories(false);
    }

    #onSubmitHandler() {
        if (this.valueTag.value == '') {
            this.nameFilterTag.removeAttribute('name');
        }

        if(this.categoryFilterTag.closest('label').hidden) {
           this.categoryFilterTag.removeAttribute('name');
        } else {
           this.valueTag.removeAttribute('name');
        }
    }
}
