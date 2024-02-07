import { Component } from "./Component";

export class DarkMode extends Component {
  constructor(...args) {
    super(...args);
  }

  onMount() {
    this.setTextDarkmode();
    this.checkDarkmode();
    this.registerListener(
      this.element,
      "click",
      this.switchDarkmode.bind(this)
    );
  }

  switchDarkmode() {
    if (localStorage.getItem("theme") === "dark") {
      localStorage.setItem("theme", "light");
      document.documentElement.classList.remove("dark");
    } else {
      localStorage.setItem("theme", "dark");
      document.documentElement.classList.add("dark");
    }

    this.setTextDarkmode();
  }

  checkDarkmode() {
    if (localStorage.getItem("theme") === "dark") {
      document.documentElement.classList.add("dark");
    } else {
      document.documentElement.classList.remove("dark");
    }
  }

  setTextDarkmode() {
    if (localStorage.getItem("theme") === "dark") {
      this.element.innerHTML = `
          <ion-icon name="moon-outline"></ion-icon>
          <p class="text-sm">Dark</p>
      `;
    } else {
      this.element.innerHTML = `
        <ion-icon name="partly-sunny"></ion-icon>
        <p class="text-sm">Light</p>
      `;
    }
  }
}
