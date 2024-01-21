export class Component {
    constructor(DOMElement) {
        this.listeners = new Map()
        this.element = DOMElement;
        this.id = 'element-' + Math.random().toString(36).substr(2, 9)
        this.element.dataset.componentId = this.id
        this.onMount()
    }

    /**
     *
     */
    destroy(){
        this.unregisterAllListeners()
        this.onUnmount()
        this.element = null;
    }

    /**
     * @abstract
     * Called when the component is created in the DOM
     */
    onMount(){}

    /**
     * @abstract
     * Called when the component is removed from the DOM
     */
    onUnmount(){}


    registerListener(element, event, callback){
        if(!this.listeners.has(element)) this.listeners.set(element, new Map())
        const elementListener = this.listeners.get(element)
        if(!elementListener.has(event)) elementListener.set(event, [])
        elementListener.get(event).push(callback)
        element.addEventListener(event, callback)
    }
    unregisterAllListeners(){
        this.listeners.forEach((elementListener, element) => {
            elementListener.forEach((callbacks, event) => {
                callbacks.forEach((callback) => {
                    element.removeEventListener(event, callback)
                })
            })
        })
    }
}