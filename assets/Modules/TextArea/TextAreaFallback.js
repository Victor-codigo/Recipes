export default class TextAreaFallBack {

    /**
     * @type {HTMLTextAreaElement}
     */
    #textAreaTag;

    /**
     * @param {HTMLTextAreaElement} textAreaTag
     */
    constructor(textAreaTag) {
        this.#textAreaTag = textAreaTag;

        this.#textAreaTag.addEventListener('input', this.#textAreaAutoGrow.bind(this));
    }

    #textAreaAutoGrow() {
        this.#textAreaTag.style.height = "5px";
        this.#textAreaTag.style.height = (this.#textAreaTag.scrollHeight) + "px";
    }

    getElement() {
        return this.#textAreaTag;
    }
}