import {Component} from "./Component";

export class MainSidebar extends Component{
    constructor(...args) {
        super(...args);
    }

    get sidebar() {
        return this.element.querySelector('#default-sidebar')
    }
    get closeBtn() {
        return this.element.querySelector('[data-drawer-close="default-sidebar"]')
    }
    get toggleBtn() {
        return this.element.querySelector('[data-drawer-toggle="default-sidebar"]')
    }
    get backdrop() {
        return this.element.querySelector('[data-drawer-backdrop="default-sidebar"]')
    }

    onMount() {
        this.registerListener(this.closeBtn, "click", this.close.bind(this));
        this.registerListener(this.toggleBtn, "click", this.toggle.bind(this));
    }

    toggle(){
        this.sidebar.classList.toggle('translate-x-0');
        if (this.backdrop) {
            this.backdrop.classList.toggle('opacity-100');
            this.backdrop.classList.toggle('pointer-events-auto');
        }
    }

    close(){
        this.sidebar.classList.remove('translate-x-0');
        if (this.backdrop) {
            this.backdrop.classList.remove('opacity-100', 'pointer-events-auto');
        }
    }
}