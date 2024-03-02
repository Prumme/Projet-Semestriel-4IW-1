import { Component } from "./Component";

export class ContextMenu extends Component {
  constructor(...args) {
    super(...args);
    this.isOpen = false;
    this._saveMenu = null;
  }

  get menu() {
    return this.element.querySelector('[data-type="items"]');
  }

  get activator() {
    return this.element.querySelector('[data-type="activator"]');
  }

  get menuPosition() {
    let activator = this.activator;
    let bounds = activator.getBoundingClientRect();
    let position = {
        top: bounds.top + bounds.height,
        left: bounds.left,
    }
    //check if the menu is out of the screen
    if (position.left + this._saveMenu.offsetWidth > window.innerWidth) {
        position.left = window.innerWidth - this._saveMenu.offsetWidth - 10;
    }
    if (position.top + this._saveMenu.offsetHeight > window.innerHeight) {
        position.top = window.innerHeight - this._saveMenu.offsetHeight - 10;
    }
    return position;
  }

  onMount() {
    this.addClassToChildElements();
    this.registerListener(this.activator, "click", this.open.bind(this));
    this.registerListener(
      document,
      "click",
      this.handleClickOutside.bind(this)
    );
  }

  addClassToChildElements() {
    for (let child of this.menu.children) {
      child.setAttribute(
        "class",
        "w-full px-4 py-2 whitespace-nowrap items-center hover:bg-white dark:hover:bg-slate-600 dark:text-text-darkmode cursor-pointer"
      );
    }
  }

  handleClickOutside(event) {
    if (this.isOpen === false) return;
    if (!this.element.contains(event.target)) {
      this.close();
    }
  }

  deplaceMenuToTheRoot(remove = false) {
    let menu = this.menu || this._saveMenu;
    this._saveMenu = menu;
    if (remove === false) {
      this.element.removeChild(menu);
      document.body.appendChild(menu);
    } else {
      document.body.removeChild(menu);
      this.element.appendChild(menu);
    }
  }


  open(event) {
    if(this.isOpen) return this.close();
    this.deplaceMenuToTheRoot();
    this.isOpen = true;
    this._saveMenu.style.transform = "scaleY(0)"
    this._saveMenu.style.transformOrigin = "center top";
    this._saveMenu.style.transition = "transform 0.2s";
    this._saveMenu.classList.remove("hidden");
    this._saveMenu.style.top = this.menuPosition.top + "px";
    this._saveMenu.style.left = this.menuPosition.left + "px";
    setTimeout(()=>{
      this._saveMenu.style.transform = "scaleY(1)";
    }, 100);

  }

  close() {
    this.isOpen = false;
    this._saveMenu.style.transformOrigin = "center top";
    this._saveMenu.style.transition = "transform 0.2s";
    this._saveMenu.style.transform = "scaleY(0)";
    setTimeout(()=>{
      this.deplaceMenuToTheRoot(true);
      this._saveMenu.classList.add("hidden");
    }, 200);
  }
}
