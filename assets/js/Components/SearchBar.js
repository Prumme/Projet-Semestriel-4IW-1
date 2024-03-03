import { Component } from "./Component";

export class SearchBar extends Component{
    constructor(...args) {
        super(...args);
        this.cooldownTimeout = null;
    }

    get input() {
        return this.element.querySelector('input');
    }

    get list () {
        return this.element.querySelector('.search-results');
    }
    
    onMount() {
       this.input.addEventListener("input", this.handleInput.bind(this));

       window.addEventListener('click', this.handleClickOutside.bind(this))
    }

    handleInput() {
        if(this.cooldownTimeout) {
            clearTimeout(this.cooldownTimeout);
        }

        this.cooldownTimeout = setTimeout(() => {
            this.fetchResults();
        }, 500);
    }

    handleClickOutside(event){
        if(!this.element) return
        if (!this.element.contains(event.target)){
            this.closeList();
        }
    }

    async fetchResults() {
       const value = this.input.value.replace(/[#]/g, '');
       const response = await fetch('/search?q=' + value);
       const html = await response.text();
       const wrapper = document.createElement('div');
       wrapper.innerHTML = html;
       const results = wrapper.querySelector('ul');
       
        if(results.children.length > 0) {
            this.openList();   
            this.list.innerHTML = wrapper.innerHTML;
        } else {
            this.closeList();
            this.list.innerHTML = '';
        }
    } 

    openList() {
        this.list.classList.remove('hidden');
    }

    closeList() {
        this.list.classList.add('hidden');
    }


}