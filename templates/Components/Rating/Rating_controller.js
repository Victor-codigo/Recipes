import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    /**
     * @type {HTMLTemplateElement}
     */
    #starTemplateTag;

    /**
     * @type {HTMLDivElement}
     */
    #starContainer;

    /**
     * @type {number}
     */
    #rating;

    connect() {
        this.#starTemplateTag = this.element.querySelector('[data-js-rating-template]');
        this.#starContainer = this.element.querySelector('[data-js-rating-stars-container]');
        this.#rating = this.element.dataset.rating;

        this.createStars(this.#rating);
    }

    /**
     * @param {number|null} starsNumber
     */
    createStars(starsNumber) {
        this.#starContainer.innerHTML = '';

        if(starsNumber===null || starsNumber===0){
            this.#starContainer.textContent = this.#starContainer.dataset.noRating;

            return;
        }

        for(let i = 0; i<starsNumber; i++){
            const starTag = this.#starTemplateTag.content.cloneNode(true);

            this.#starContainer.appendChild(starTag);
        }
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {number} event.detail.content.rating
     */
    handleSetRatingEvent ({ detail: { content } }) {
        this.createStars(content.rating);
    }
}