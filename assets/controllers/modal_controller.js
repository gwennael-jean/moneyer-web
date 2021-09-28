import {Controller} from 'stimulus';
import {Modal} from 'bootstrap';

export default class extends Controller {
    connect() {
        this.modal = new Modal(this.element)
    }

    open(event) {
        event.preventDefault()

        const xhr = new XMLHttpRequest()
        xhr.open("GET", event.target.getAttribute('href'), true)
        xhr.setRequestHeader("X-Requested-With", 'XMLHttpRequest')

        this.element.querySelector('.modal-title').innerHTML = event.target.dataset.title
        this.element.querySelector('.modal-body').innerHTML = "<div class='text-center'>Loading ...</div>"
        this.modal.show()

        xhr.onreadystatechange = (data) => {
            if (xhr.readyState === 4 && xhr.status === 200) {
                this.fill(xhr.responseText, event.target)
            }
        }

        xhr.send()
    }

    fill(content, element) {
        var parser = new DOMParser()
        var response = parser.parseFromString(content, 'text/html')

        this.element.querySelector('.modal-body').innerHTML = response.querySelector('[data-part-body]').innerHTML
        this.element.querySelector('.modal-footer').innerHTML = response.querySelector('[data-part-footer]').innerHTML

        if (null !== this.element.querySelector('.modal-body form')) {

            if (!this.element.querySelector('.modal-body form').hasAttribute("action")) {
                this.element.querySelector('.modal-body form').setAttribute("action", element.getAttribute('href'))
            }

            if (null !== this.element.querySelector('.modal-footer [type=submit]')) {
                let formId = this.element.querySelector('.modal-body form').getAttribute('id')

                if (null === formId) {
                    formId = "form-" + Date.now();
                    this.element.querySelector('.modal-body form').setAttribute('id', formId)
                }

                this.element.querySelector('.modal-footer [type=submit]').setAttribute("form", formId)
            }
        }
    }
}
