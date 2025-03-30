import { Controller } from '@hotwired/stimulus';
import * as config from 'App/Config';
import * as locale from 'App/Modules/Locale';
import * as communication from 'App/Modules/ControllerCommunication'

export default class extends Controller {
    /**
     * @type {HTMLHeadElement}
     */
    #titleTag;
    get titleTag() { return this.#titleTag };

    /**
     * @type {HTMLSpanElement}
     */
    #dateTag;
    get dateTag() { return this.#dateTag };

    /**
     * @type {HTMLImageElement}
     */
    #imageTag;
    get imageTag() { return this.#imageTag };

    /**
     * @type {HTMLElement}
     */
    #authorTag;

    /**
     * @type {HTMLParagraphElement}
     */
    #categoryTag;

    /**
     * @type {HTMLParagraphElement}
     */
    #descriptionTag;
    get descriptionTag() { return this.#descriptionTag };

    /**
     * @type {HTMLSpanElement}
     */
    #preparationTimeTag;

    /**
     * @type {HTMLSpanElement}
     */
    #ratingTag;

    /**
     * @type {HTMLInputElement}
     */
    #publicTag;
    /**
     * @type {HTMLDivElement}
     */
    #ingredientsTag;

    /**
     * @type {HTMLDivElement}
     */
    #stepsTag;

    connect () {
        this.#titleTag = this.element.querySelector('[data-js-recipe-title]');
        this.#dateTag = this.element.querySelector('[data-js-recipe-date]');
        this.#imageTag = this.element.querySelector('[data-js-recipe-image]');
        this.#authorTag = this.element.querySelector('[data-js-recipe-author]');
        this.#categoryTag = this.element.querySelector('[data-js-recipe-category]');
        this.#descriptionTag = this.element.querySelector('[data-js-recipe-description]');
        this.#preparationTimeTag = this.element.querySelector('[data-js-recipe-preparation-time]');
        this.#ratingTag = this.element.querySelector('[data-controller="RatingComponent"]');
        this.#publicTag = this.element.querySelector('[data-js-recipe-public]');
        this.#ingredientsTag = this.element.querySelector('[data-js-recipe-ingredients]');
        this.#stepsTag = this.element.querySelector('[data-js-recipe-steps]');
        this.#stepsTag = this.element.querySelector('[data-js-recipe-steps]');
    }

    /**
     * @param {config.RecipeData} data
     */
    setRecipeData(data) {
        this.#titleTag.textContent = data.name;
        this.#dateTag.textContent = locale.formatDateToLocale(data.createdOn);
        this.#imageTag.src = data.image;
        this.#authorTag.textContent = data.userOwnerName;
        this.setCategory(data.category);
        this.#descriptionTag.textContent = data.description === null ? '' : data.description;
        this.setPreparationTime(data.preparation_time);
        this.setPublic(data.public)
        this.setPublic(data.public);
        this.setIngredients(data.ingredients);
        this.setSteps(data.steps);
        communication.sendMessageToChildController(this.#ratingTag,'setRating', {
            rating: data.rating
        });
    }

    /**
     * @param {string|null} preparationTime
     */
    setPreparationTime(preparationTime) {
        console.log(this.#preparationTimeTag);
        this.#preparationTimeTag.textContent = preparationTime === null
            ? this.#preparationTimeTag.dataset.notSpecified
            : preparationTime;
    }

    /**
     * @param {string} category
     */
    setCategory(category) {
        const categoryAsDataset = category
            .toLowerCase()
            .replace(/_[a-z]/g, (match) => match.toUpperCase().replace('_',''));;
        this.#categoryTag.textContent = this.#categoryTag.dataset[categoryAsDataset];
    }


    /**
     * @param {boolean} recipePublic
     */
    setPublic(recipePublic) {
        this.#publicTag.textContent = recipePublic
            ? this.#publicTag.dataset.public
            : this.#publicTag.dataset.private;
    }

    /**
     * @param {string[]} ingredients
     */
    setIngredients(ingredients) {
        this.#ingredientsTag.innerHTML = '';
        ingredients.forEach(ingredient => {
            const ingredientTag = document.createElement('li');
            ingredientTag.classList.add('ingredients__ingredient');
            ingredientTag.textContent = ingredient;
            this.#ingredientsTag.appendChild(ingredientTag);
        });
    }

    /**
     * @param {string[]} steps
     */
    setSteps(steps) {
        this.#stepsTag.innerHTML = '';
        steps.forEach(step => {
            const stepTag = document.createElement('li');
            stepTag.classList.add('steps__step');
            stepTag.textContent = step;
            this.#stepsTag.appendChild(stepTag);
        });
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {config.RecipeData} event.detail.content.itemData
     */
    handleMessageHomeListRecipeInfo({ detail: { content } }) {
        this.setRecipeData(content.itemData);
    }
}