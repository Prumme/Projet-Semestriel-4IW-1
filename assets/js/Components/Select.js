import {Component} from "./Component";

export class Select extends Component{
    constructor(...args) {
        super(...args);
    }
    get placeholderString(){
        return this.element.dataset.placeholder
    }
    get multiple(){
        return this.element.hasAttribute('data-multiple')
    }
    get label(){
        return this.element.querySelector('[data-type="label"]')
    }
    get inputs(){
        return this.element.querySelectorAll('input')
    }
    get selectedInputs(){
        return this.element.querySelectorAll('input:checked')
    }
    onMount() {
        this.inputs.forEach(input => {
            this.registerListener(input, 'change', this.handleUpdate.bind(this))
        })

        this.registerListener(this.element, 'click', e => {
            this.element.querySelector('ul').classList.toggle('hidden')
        })

        window.addEventListener('click', this.handleClickOutside.bind(this))

        this.handleUpdate() //initial update
    }

    handleClickOutside(event){
        if(!this.element) return
        if(this.element.classList.contains('hidden')) return
        if (!this.element.contains(event.target)){
            this.element.querySelector('ul').classList.add('hidden')
        }
    }

    handleUpdate(){
        const checkedInputs = this.selectedInputs
        if (checkedInputs.length === 0){
            this.label.innerText = this.placeholderString
        }else if (checkedInputs.length === 1){
            this.label.innerText = checkedInputs[0].nextElementSibling.innerText
        }else{
            this.label.innerText = checkedInputs.length + ' éléments séléctionnés'
        }
        this.element.querySelectorAll('li').forEach(li => {
            li.classList.remove('bg-primary-200')
        })

        checkedInputs.forEach(input => {
            input.closest('li').classList.add('bg-primary-200')
        })

        if(!this.multiple) this.element.querySelector('ul').classList.add('hidden')
    }
}