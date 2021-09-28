import {Controller} from 'stimulus';
import {Modal} from 'bootstrap';

export default class extends Controller {
    static targets = ["header", "title", "body", "footer"];

    connect() {
        this.modal = new Modal(this.element)
    }

    open(event) {
        event.preventDefault()

        const xhr = new XMLHttpRequest()
        xhr.open("GET", event.target.getAttribute('href'), true)
        xhr.setRequestHeader("X-Requested-With", 'XMLHttpRequest')

        this.titleTarget.innerHTML = event.target.dataset.title
        this.bodyTarget.innerHTML = "<div class='text-center'>Loading ...</div>"
        this.footerTarget.innerHTML = ""
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

        this.bodyTarget.innerHTML = response.querySelector('[data-part-body]').innerHTML
        this.footerTarget.innerHTML = response.querySelector('[data-part-footer]').innerHTML

        if (null !== this.bodyTarget.querySelector('form')) {

            if (!this.bodyTarget.querySelector('form').hasAttribute("action")) {
                this.bodyTarget.querySelector('form').setAttribute("action", element.getAttribute('href'))
            }

            if (null !== this.footerTarget.querySelector('[type=submit]')) {
                let formId = this.bodyTarget.querySelector('form').getAttribute('id')

                if (null === formId) {
                    formId = "form-" + Date.now();
                    this.bodyTarget.querySelector('form').setAttribute('id', formId)
                }

                this.footerTarget.querySelector('[type=submit]').setAttribute("form", formId)
            }
        }
    }
}
