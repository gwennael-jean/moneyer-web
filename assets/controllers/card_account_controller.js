import {Controller} from 'stimulus';

import { Modal } from 'bootstrap';

export default class extends Controller {
    connect() {
        this.modalElement = document.getElementById('modal-form')
        this.modal = new Modal(this.modalElement)
    }

    openModal(event) {
        event.preventDefault()
        const httpRequest = new XMLHttpRequest();
        httpRequest.open("GET", event.target.getAttribute('href'), true);
        httpRequest.onreadystatechange = (data) => {
            if (httpRequest.readyState === 4 && httpRequest.status === 200) {
                this.modalElement.querySelector('.modal-body').innerHTML = httpRequest.responseText
                this.modal.show()
            }
        }
        httpRequest.send();
    }
}
