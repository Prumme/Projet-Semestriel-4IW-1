import {Component} from "./Component";

export class SignatureDrawZone extends Component {
    constructor(...args) {
        super(...args);
        this.isDrawing = false
    }

    get canvas(){
        return this.element.querySelector('canvas')
    }

    onMount() {
        this.context = this.canvas.getContext('2d')
        this.canvas.addEventListener('mousedown', this.startDrawing.bind(this))
        this.canvas.addEventListener('mousemove', this.draw.bind(this))
        this.canvas.addEventListener('mouseup', this.stopDrawing.bind(this))
    }

    startDrawing(e){
        this.isDrawing = true
        this.draw(e)
    }

    draw(e){
        if(!this.isDrawing) return
        this.context.lineWidth = 2
        this.context.lineCap = 'round'
        this.context.lineTo(e.offsetX, e.offsetY)
        this.context.stroke()
        this.context.beginPath()
        this.context.moveTo(e.offsetX, e.offsetY)
    }

    stopDrawing(e){
        this.isDrawing = false
        this.element.querySelector('[name="signature"]').value = this.getImageData()
    }

    getImageData(){
        return this.canvas.toDataURL();
    }
}