import { Carousel } from "./Components/Carousel";
import { Component } from "./Components/Component";
import { SearchInput } from "./Components/SearchInput";
import { Select } from "./Components/Select";
import { SignatureDrawZone } from "./Components/SignatureDrawZone";
import { DarkMode } from "./Components/DarkMode";

export const COMPONENTS = {
  select: Select,
  searchInput: SearchInput,
  signatureDrawZone: SignatureDrawZone,
  carousel: Carousel,
  darkMode: DarkMode,
  contextMenu: ContextMenu,
};
export default function bootstrap() {
  initializeComponents();
  initializeWatcher();
}

function initializeComponents() {
  console.log("[BOOSTRAP] initComponents");
  window.$app = App.getInstance();
  const components = document.querySelectorAll("[data-bind-component]");
  components.forEach((component) => {
    const componentName = component.getAttribute("data-bind-component");
    if (COMPONENTS[componentName]) {
      const componentInstance = new COMPONENTS[componentName](component);
      $app.registerComponent(componentInstance);
    }
  });
  console.log("[BOOSTRAP] initComponents done", $app);
}

function initializeComponents() {
  console.log("[BOOSTRAP] initComponents");
  window.$app = App.getInstance();
  const components = document.querySelectorAll("[data-bind-component]");
  components.forEach((component) => {
    const componentName = component.getAttribute("data-bind-component");
    if (COMPONENTS[componentName]) {
      const componentInstance = new COMPONENTS[componentName](component);
      $app.registerComponent(componentInstance);
    }
  });
  console.log("[BOOSTRAP] initComponents done", $app);
}

function initializeWatcher() {
  console.log("[BOOSTRAP] initWatcher");
  const app = App.getInstance();
  //observe new DOM elements with the attribute data-apa-component
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (node.nodeType === Node.ELEMENT_NODE) {
          //check in children
          const components = node.querySelectorAll("[data-bind-component]");
          components.forEach((component) => {
            const componentName = component.getAttribute("data-bind-component");
            if (COMPONENTS[componentName]) {
              const componentInstance = new COMPONENTS[componentName](
                component
              );
              app.registerComponent(componentInstance);
            }
          });
          if (node.hasAttribute("data-bind-component")) {
            const componentName = node.getAttribute("data-bind-component");
            if (COMPONENTS[componentName]) {
              const component = new COMPONENTS[componentName](node);
              app.registerComponent(component);
            }
          }
        }
      });
      mutation.removedNodes.forEach((node) => {
        if (node.nodeType === Node.ELEMENT_NODE) {
          //check in children
          const componentsDoms = node.querySelectorAll("[data-bind-component]");
          componentsDoms.forEach((el) => {
            if (!el.hasAttribute("data-component-id")) return;
            const componentId = el.getAttribute("data-component-id");
            const component = app.getComponent(componentId);
            if (component) app.unregisterComponent(component);
          });
          if (!node.hasAttribute("data-bind-component")) return;
          const componentId = node.getAttribute("data-component-id");
          const component = app.getComponent(componentId);
          if (component) app.unregisterComponent(component);
        }
      });
    });
  });
  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });
}

class App {
  static instance = null;
  static getInstance() {
    if (App.instance === null) App.instance = new App();
    return App.instance;
  }
  constructor() {
    this.components = new Map();
  }
  registerComponent(component) {
    if (!(component instanceof Component))
      throw new Error("Component must be an instance of Component");
    if (component.id) {
      this.components.set(component.id, component);
    } else {
      throw new Error("Component must have an id");
    }
  }
  unregisterComponent(component) {
    if (!(component instanceof Component))
      throw new Error("Component must be an instance of Component");
    if (component.id) {
      component.destroy();
      this.components.delete(component.id);
    }
  }
  getComponent(id) {
    return this.components.get(id);
  }
}
