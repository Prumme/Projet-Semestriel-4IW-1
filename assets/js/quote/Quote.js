import {BillingRow} from "./BillingRow.js";
export class Quote{
    static instance= null;

    /**
     * @returns {Quote}
     */
    static getInstance(){
        if(Quote.instance === null) throw new Error("Quote is not initialized");
        return Quote.instance;
    }
    constructor() {
        /**
         * @type {Map<String,BillingRow>}
         */
        this.billingsRows = new Map()
    }
    static init(){
        if(Quote.instance === null){
            Quote.instance = new Quote();
        }else throw new Error("Quote is already initialized");
    }

    exploreBillingRows(){
        let billingRowsDOMElements = document.querySelectorAll('[data-billing-row="true"]')
        billingRowsDOMElements.forEach((billingRowDOMElement) => {
            this.registerBillingRow(billingRowDOMElement)
        })

        this.billingsRows.forEach((billingRow,id) => {
            if(!document.body.contains(billingRow.element)){
                billingRow.unbind()
                this.billingsRows.delete(id)
            }
        })
    }
    registerBillingRow(billingRowDOMElement){
        if(billingRowDOMElement.id) return;
        let billingRowId = 'billing-row-' + Math.random().toString(36).substr(2, 9)
        billingRowDOMElement.id = billingRowId
        this.billingsRows.set(billingRowId, new BillingRow(billingRowId,billingRowDOMElement));
    }

    getBillingRow(id){
        return this.billingsRows.get(id)
    }
}

export function initializeQuoteSystem(){
    window.Quote = Quote;
    Quote.init();
    return Quote.getInstance();
}