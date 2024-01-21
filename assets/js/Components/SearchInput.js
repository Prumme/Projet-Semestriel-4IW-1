import {Component} from "./Component";

export class SearchInput extends Component{
    constructor(...args) {
        super(...args);
    }
    get list(){
        return this.element.querySelector(".search-input-list")
    }

    get callBackFunctionString(){
        return this.element.dataset.callbackFunction ?? null
    }

    get listItems(){
        return this.list.querySelectorAll("[data-value]")
    }

    get input(){
        return this.element.querySelector('input')
    }
    onMount() {
        this.registerListener(this.input, "focus", this.handleFocus.bind(this))
        this.registerListener(this.input, "input", this.handleInput.bind(this))
        this.listItems.forEach((listItem) => {
            this.registerListener(listItem, "click", this.handleItemClick.bind(this))
        })
    }

    handleFocus(event){
        this.openList()
        let self = this
        document.addEventListener("click", function(event){
            if(event.target.closest("[id^='search-input-']")) return;
            if(self.element) self.closeList()
            document.removeEventListener("click", this)
        })
    }

    handleInput(event){
        const search = event.currentTarget.value
        const items = this.listItems
        items.forEach(function(listItem){
            const label = listItem.dataset.label
            if(label.toLowerCase().includes(search.toLowerCase())){
                listItem.classList.remove("hidden")
            }else{
                listItem.classList.add("hidden")
            }
        })
    }

    handleItemClick(event){
        const label = event.currentTarget.dataset.label
        const value = JSON.parse(event.currentTarget.dataset.value)
        this.input.value = label
        this.closeList()
        if(this.callBackFunctionString){
            const callBackFunction = eval(this.callBackFunctionString)
            callBackFunction(value,this.input)
        }
    }
    openList(){
        this.list.classList.remove("hidden")
    }

    closeList(){
        this.list.classList.add("hidden")
    }
}