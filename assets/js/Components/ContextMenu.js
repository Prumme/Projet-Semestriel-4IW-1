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
    this.deplaceMenuToTheRoot();
    this.isOpen = true;
    let position = {
      top: event..clientY,
      left: event.clientX,
    };
    this._saveMenu.style.top = position.top + "px";
    this._saveMenu.style.left = position.left + "px";
    this._saveMenu.classList.remove("hidden");
  }

  close() {
    this.deplaceMenuToTheRoot(true);
    this.isOpen = false;
    this._saveMenu.classList.add("hidden");
  }
}
