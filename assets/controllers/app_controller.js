import {Controller} from 'stimulus';

export default class extends Controller {
    static targets = ["modal"];

    openModal(event) {
        const modalController = this.application.getControllerForElementAndIdentifier(this.modalTarget, "modal")
        modalController.open(event)
    }
}
