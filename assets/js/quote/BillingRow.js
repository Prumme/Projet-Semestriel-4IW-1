export class BillingRow{
    constructor(id,billingRowDOMElement) {
        this.id = id;
        this.element = billingRowDOMElement;
        this.bindEvents()
        this.applyVAT()

        let {price,total} = this.getInputs()
        price.setAttribute('disabled',true)
        total.setAttribute('disabled',true)
    }

    /**
     * Get all inputs of the billing row DOM element
     * @returns {{price: Element, unit: Element, quantity: Element, vat: Element, total: Element}}
     */
    getInputs(){
        let inputDiscountTypes = this.element.querySelectorAll('[name$="[discount_type]"] ')
        let inputDiscountValue = this.element.querySelector('[name$="[discount_value]"]')
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
            discount:{
                types: inputDiscountTypes,
                value: inputDiscountValue
            }
        }
    }

    getCheckedDiscountType(){
        let allDiscountTypes = this.element.querySelectorAll('[name$="[discount_type]"]')
        let checkedDiscountType = null
        allDiscountTypes.forEach((type) => {
            if(type.checked) checkedDiscountType = type
        })
        return checkedDiscountType
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
        inputs.discount.types.forEach((type) => {
            type.addEventListener('change',this.handleInputChange.bind(this))
        })
        inputs.discount.value.addEventListener('change',this.handleInputChange.bind(this))
    }
    handleInputChange(event){
        let inputs = this.getInputs()
        let qt = Math.round(inputs.quantity.value)
        inputs.quantity.value =  qt > 0 ? qt : 1
        inputs.unit.value = this.toValidAmmount(inputs.unit.value)
        inputs.vat.value =  inputs.vat.value ? this.toValidAmmount(inputs.vat.value) : 20
        this.applyVAT(inputs)
    }

    calulateHTPrice(inputs){
        let priceHT = inputs.unit.value * Math.round(inputs.quantity.value)
        let priceHTWithDiscount = this.applyDiscount(inputs,priceHT)
        inputs.price.value = this.toValidAmmount(priceHTWithDiscount)
        return priceHTWithDiscount
    }
    applyDiscount(inputs,priceHT){
        let selectedDiscountType = this.getCheckedDiscountType()
        if(!selectedDiscountType) return this.toValidAmmount(priceHT)
        let discountType = Number(selectedDiscountType.value)
        let discountValue = Number(inputs.discount.value.value)
        if(!discountValue) return this.toValidAmmount(priceHT)
        if(discountType === 1){
            priceHT = this.toValidAmmount(priceHT - (priceHT * discountValue / 100))
        }else{
            priceHT = this.toValidAmmount(priceHT - discountValue)
        }
        return priceHT
    }

    applyVAT(inputs = null){
        if(!inputs) inputs = this.getInputs()
        let priceHT = this.calulateHTPrice(inputs)
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