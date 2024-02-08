import { Component } from "./Component";

export class Carousel extends Component {
    constructor(element, ...args) {
        super(element, ...args);
        this.currentIndex = 0;
        this.cards = Array.from(this.element.querySelectorAll('.review-card'));
        this.transitionDuration = 1000;
    }

    get previousButton() {
        return this.element.querySelector('#btn-previous');
    }

    get nextButton() {
        return this.element.querySelector('#btn-next');
    }

    get indicators(){
        return this.element.querySelectorAll('.indicator')
    }

    onMount() {
        this.registerListener(this.previousButton, 'click', this.previousCard.bind(this));
        this.registerListener(this.nextButton, 'click', this.nextCard.bind(this));
        this.updateIndicators()
        this.autoChange();
    }

    showCard(index) {
        this.cards.forEach((card, i) => {
            if (i === index) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    updateIndicators(index = 0) {
        this.indicators.forEach((indicator, i) => {
            if (i === index) {
                indicator.classList.add('!w-20','border-primary-500');
            } else {
                indicator.classList.remove('!w-20','border-primary-500');
            }
        });
    }

    previousCard(e) {
        this.currentIndex = (this.currentIndex === 0) ? this.cards.length - 1 : this.currentIndex - 1;
        this.showCard(this.currentIndex);
        this.updateIndicators(this.currentIndex);
    }

    nextCard(e) {
        this.currentIndex = (this.currentIndex === this.cards.length - 1) ? 0 : this.currentIndex + 1;
        this.showCard(this.currentIndex);
        this.updateIndicators(this.currentIndex);
    }

    autoChange() {
        setInterval(() => {
            this.nextCard();
        }, 5000);
    }
}
