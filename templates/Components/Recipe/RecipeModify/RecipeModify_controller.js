import { Controller } from '@hotwired/stimulus';
import * as form from 'App/Modules/form';
import * as communication from 'App/Modules/ControllerCommunication'
import ModalManager from 'App/Modules/ModalManager/ModalManager';
import TextAreaFallBack from 'App/Modules/TextArea/TextAreaFallback';

export default class RecipeModifyController extends Controller {

    /**
     * @type {HTMLInputElement}
     */
    #id;

    /**
     * @type {HTMLInputElement}
     */
    #nameTag;

    /**
     * @type {HTMLTextAreaElement}
     */
    #descriptionTag;

    /**
     * @type {HTMLInputElement}
     */
    #preparationTimeTag;

    /**
     * @type {HTMLSelectElement}
     */
    #categoryTag;

    /**
     * @type {HTMLInputElement}
     */
    #publicTag;

    /**
     * @type {HTMLElement}
     */
    #dropzoneComponentTag;

    /**
     * @type {HTMLInputElement}
     */
    #ingredientsTag;

    /**
     * @type {HTMLTextAreaElement}
     */
    #stepsTag;

    /**
     * @type {HTMLElement}
     */
    #imageAvatar;

    connect() {
        this.#id = this.element.querySelector('[data-js-recipe-id]');
        this.#nameTag = this.element.querySelector('[data-js-recipe-name]');
        this.#descriptionTag = this.element.querySelector('[data-js-recipe-description]');
        this.#preparationTimeTag = this.element.querySelector('[data-js-recipe-preparation-time]');
        this.#categoryTag = this.element.querySelector('[data-js-recipe-category]');
        this.#publicTag = this.element.querySelector('[data-js-recipe-public]');
        this.#dropzoneComponentTag = this.element.querySelector('[data-controller="DropZoneComponent"]');
        this.#ingredientsTag = this.element.querySelector('[data-js-ingredients]');
        this.#stepsTag = this.element.querySelector('[data-js-steps]');
        this.#imageAvatar = this.element.querySelector('[data-controller="ImageAvatarComponent"]');
        new TextAreaFallBack(/** @type {HTMLTextAreaElement} */(document.getElementById('description')));

        this.formValidate();
    }


    formValidate() {
        form.validate(this.element, () => {
            const ingredientsAreValid = this.#validateIngredients();
            const stepsAreValid = this.#validateSteps();

            return ingredientsAreValid && stepsAreValid;
        });
    }


    /**
     * @returns {boolean}
     */
    #validateIngredients() {
        const ingredients = this.application.getControllerForElementAndIdentifier(this.#ingredientsTag, 'RecipeItemAddComponent');
        const ingredientsHasItems = ingredients.hasItemsWithText();
        const ingredientsAreValid = ingredients.isValid();

        if (!ingredientsAreValid || !ingredientsHasItems) {
            ingredients.setIsValidStyles(false);
        }

        return ingredientsAreValid && ingredientsHasItems;
    }

    /**
     * @returns {boolean}
     */
    #validateSteps() {
        const steps = this.application.getControllerForElementAndIdentifier(this.#stepsTag, 'RecipeItemAddComponent');
        const stepsHasItems = steps.hasItemsWithText();
        const stepsAreValid = steps.isValid();

        if (!stepsAreValid || !stepsHasItems) {
            steps.setIsValidStyles(false);
        }

        return stepsAreValid && stepsHasItems;
    }

    #clearForm() {
        this.element.reset();
        this.element.classList.remove('was-validated');

        this.#sendMessageClearToDropZone();
        this.#sendMessageClearToIngredientsAdd();
        this.#sendMessageClearToStepsAdd();
    }


    #setFormFieldValues(recipeData){
        this.#id.value = recipeData.id;
        this.#nameTag.value = recipeData.name;
        this.#descriptionTag.value = recipeData.description;
        this.#preparationTimeTag.value = recipeData.preparation_time;
        this.#categoryTag.value = recipeData.category;
        this.#publicTag.checked = recipeData.public;
        this.#sendMessageAvatarSetImageEventToImageAvatarComponent(recipeData.image);

        recipeData.ingredients.forEach((ingredient) => this.#sendMessageIngredientAddToIngredientsAdd(ingredient));
        recipeData.steps.forEach((step) => this.#sendMessageStepAddToIngredientsAdd(step));
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.imageRemove
     */
    setImageAvatarAsRemoved(event) {
        let imageRemovedField = this.element.querySelector('[data-js-image-remove]');

        if (event.detail.imageRemove) {
            imageRemovedField.value = "true";

            return;
        }

        imageRemovedField.removeAttribute('value');
    }



    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {boolean} event.detail.content.showedFirstTime
     * @param {HTMLElement} event.detail.content.triggerElement
     * @param {ModalManager} event.detail.content.modalManager
     */
    handleMessageBeforeShowed({ detail: { content } }) {
        this.#clearForm();
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {object} event.detail.content.itemData
     */
    handleMessageHomeListItemModify({ detail: { content } }) {
        this.#setFormFieldValues(content.itemData);
    }


    #sendMessageClearToDropZone() {
        communication.sendMessageToChildController(this.#dropzoneComponentTag, 'clear');
    }

    #sendMessageClearToIngredientsAdd() {
        communication.sendMessageToChildController(this.#ingredientsTag, 'clear');
    }

    #sendMessageClearToStepsAdd() {
        communication.sendMessageToChildController(this.#stepsTag, 'clear');
    }

    /**
     * @param {string} value
     */
    #sendMessageIngredientAddToIngredientsAdd(value) {
        communication.sendMessageToChildController(this.#ingredientsTag, 'itemAdd', { value: value });
    }

    /**
     * @param {string} value
     */
    #sendMessageStepAddToIngredientsAdd(value) {
        communication.sendMessageToChildController(this.#stepsTag, 'itemAdd', { value: value });
    }

    /**
     * @param {string} recipeImageUrl
     */
    #sendMessageAvatarSetImageEventToImageAvatarComponent(recipeImageUrl) {
        communication.sendMessageToChildController(this.#imageAvatar, 'avatarSetImage', { imageUrl: recipeImageUrl });
    }
}
