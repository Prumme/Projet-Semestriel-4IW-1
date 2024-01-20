export class BillingRow{
    constructor(id,billingRowDOMElement) {
        this.id = id;
        this.element = billingRowDOMElement;
        this.bindEvents()
        this.applyVAT()
    }

    /**
     * Get all inputs of the billing row DOM element
     * @returns {{price: Element, unit: Element, quantity: Element, vat: Element, total: Element}}
     */
    getInputs(){
        let inputPrice = this.element.querySelector('[name$="[price]"]')
        let inputUnit =  this.element.querySelector('[name$="[unit]"]')
        let inputQuantity =  this.element.querySelector('[name$="[quantity]"]')
        let inputVAT =  this.element.querySelector('[name$="[vat]"]')
        let inputTotal =  this.element.querySelector('[name$="[total]"]')
        return {
            price: inputPrice,
            unit: inputUnit,
            quantity: inputQuantity,
            vat: inputVAT,
            total: inputTotal,
        }
    }

    setVal(name,value){
        let input = this.element.querySelector(`[name$="[${name}]"]`)
        input.value = value
        this.handleInputChange()
    }

    bindEvents(){
        let inputs = this.getInputs()
        inputs.unit.addEventListener('change',this.handleInputChange.bind(this))
        inputs.quantity.addEventListener('change',this.handleInputChange.bind(this))
        inputs.price.addEventListener('change',this.handleInputChange.bind(this))
        inputs.vat.addEventListener('change',this.handleInputChange.bind(this))
    }
    handleInputChange(event){
        let inputs = this.getInputs()
        let qt = Math.round(inputs.quantity.value)
        inputs.quantity.value =  qt > 0 ? qt : 1
        inputs.unit.value = this.toValidAmmount(inputs.unit.value)
        let priceHT = inputs.unit.value * Math.round(inputs.quantity.value)
        inputs.price.value = this.toValidAmmount(priceHT)
        inputs.vat.value =  inputs.vat.value ? this.toValidAmmount(inputs.vat.value) : 20
        this.applyVAT()
    }

    applyVAT(){
        let inputs = this.getInputs()
        let priceHT = inputs.price.value
        inputs.total.value = this.toValidAmmount(priceHT * (1 + inputs.vat.value / 100))
    }

    toValidAmmount(value){
        return Math.round(value * 100) / 100
    }

    /**
     * Unbind all events listeners for this billing row
     */
    unbind(){
        let inputs = this.getInputs()
        inputs.unit.removeEventListener('change',this.handleInputChange.bind(this))
        inputs.quantity.removeEventListener('change',this.handleInputChange.bind(this))
        inputs.price.removeEventListener('change',this.handleInputChange.bind(this))
        inputs.vat.removeEventListener('change',this.handleInputChange.bind(this))
    }
}