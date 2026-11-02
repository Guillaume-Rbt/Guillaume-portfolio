import { createElement, $$ } from "../utils/dom";
import { Events } from "../utils/events";

export class Tabs extends HTMLElement {


    tabsContent = new Map<HTMLElement, HTMLElement>();
    currentTab: HTMLElement | null = null;
    tabList = this.getAttribute('tabs-list')?.split(',') || [];
    tabsElements: HTMLElement[] = [];
    contents = [...$$(':scope > *', this)] as HTMLElement[];


    get tabsNumber() {
        return this.tabList.length || 0;
    }

    static get observedAttributes() {
        return ['tabs-list'];
    }


    constructor() {
        super();
        this.onTabClick = this.onTabClick.bind(this);
    }


    attributeChangedCallback(name: string, oldValue: any, newValue: any) {
        switch (name) {
            case 'tabs-list':

                this.tabList = newValue?.split(',') || [];
                if (this.tabList.length === 0) {
                    console.error(`No tabs found`);
                    return;
                }
                this.render();
                break;
        }
    }


    addTabs() {
        this.tabList.forEach((tab) => {
            const tabElement = createElement('button', "", "tab", "link", "lnk-primary");
            tabElement.textContent = tab;
            this.tabsElements.push(tabElement);
        });
    }


    connectedCallback() {

        this.style.display = "block";

        if (this.tabsNumber === 0 || this.contents.length === 0) {
            console.error(`No tabs or content found`);
            return;
        }
        if (this.contents.length !== this.tabsNumber) {
            console.error(`Mismatch between number of tabs (${this.tabsNumber}) and content sections (${this.contents.length})`);
            return;
        }
    }


    render() {
        this.removeEvents();
        this.innerHTML = "";
        const wrapper = createElement('div', "", "flex", "flex-col", "cursor-pointer");
        const content = createElement('div', "", "grid", "grid-cols-1", "grid-rows-1");
        this.addTabs();
        const tabsContainer = createElement('div', "", "tabs-wrapper", "flex", "gap-1.5");
        this.currentTab = this.tabsElements[0];
        this.currentTab.classList.add("selected");
        this.tabsElements.forEach(tab => tabsContainer.appendChild(tab));

        this.contents.forEach((c, i) => {
            c.classList.add("grid-col-1", "grid-row-1");
            content.appendChild(c);
            this.tabsContent.set(this.tabsElements[i], c);
            if (i !== 0) {
                c.classList.add("opacity-0", "pointer-events-none");
            }
        });

        wrapper.appendChild(tabsContainer);
        wrapper.appendChild(content);
        this.appendChild(wrapper);
        this.addEvents();
    }

    onTabClick(e: Event) {
        e.preventDefault();
        this.currentTab = e.currentTarget as HTMLElement;
        this.tabsContent.forEach((content, tab) => {
            tab.classList.toggle("selected", tab === this.currentTab);
            content.classList.toggle("opacity-0", tab !== this.currentTab);
            content.classList.toggle("pointer-events-none", tab !== this.currentTab);
        });
    }

    addEvents() {
        this.tabsElements.forEach((button) => {
            button.addEventListener(Events.click, this.onTabClick);
        });
    }

    disconnectedCallback() {
        this.removeEvents();
    }

    removeEvents() {
        this.tabsElements.forEach((button) => {
            button.removeEventListener(Events.click, this.onTabClick);
        });
    }
}



