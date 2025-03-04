import { Controller } from '@hotwired/stimulus';
import * as form from 'App/Modules/form';
import * as communication from 'App/Modules/ControllerCommunication'
import ModalManager from 'App/Modules/ModalManager/ModalManager';
import TextAreaFallBack from 'App/Modules/TextArea/TextAreaFallback';

export default class RecipeCreateController extends Controller {

    /**
     * @type {HTMLElement}
     */
    #dropzoneComponentTag;

    /**
     * @type {HTMLElement}
     */
    #ingredients;

    /**
     * @type {HTMLElement}
     */
    #steps;

    connect() {
        this.#dropzoneComponentTag = this.element.querySelector('[data-controller="DropZoneComponent"]');
        this.#ingredients = this.element.querySelector('[data-js-ingredients]');
        this.#steps = this.element.querySelector('[data-js-steps]');
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
        const ingredients = this.application.getControllerForElementAndIdentifier(this.#ingredients, 'RecipeItemAddComponent');
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
        const steps = this.application.getControllerForElementAndIdentifier(this.#steps, 'RecipeItemAddComponent');
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

    #sendMessageClearToDropZone() {
        communication.sendMessageToChildController(this.#dropzoneComponentTag, 'clear');
    }

    #sendMessageClearToIngredientsAdd() {
        communication.sendMessageToChildController(this.#ingredients, 'clear');
    }

    #sendMessageClearToStepsAdd() {
        communication.sendMessageToChildController(this.#steps, 'clear');
    }
}
