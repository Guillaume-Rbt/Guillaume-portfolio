import { createElement } from "../utils/dom";

export class Tabs extends HTMLElement {


    tabsContent = new Map<HTMLElement, HTMLElement>();
    currentTab: HTMLElement | null = null;
    tabList = this.getAttribute('tabs-list')?.split(',') || [];
    contents = [...this.querySelectorAll(':scope > *')];

    get tabsNumber() {
        return this.tabList.length || 0;
    }

    static get observedAttributes() {
        return ['tabs-list'];
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


    getTabs() {

        const tabsContainer = createElement('div', "", "tabs-wrapper");

        this.tabList.forEach(tab => {
            const tabElement = createElement('button', "", "tab");
            tabElement.textContent = tab;
            tabsContainer.appendChild(tabElement);
        });


        return tabsContainer;


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


        this.render();

    }


    render() {
        const wrapper = createElement('div', "", "tabs-wrapper");
        const content = createElement('div', "", "tabs-content");
        const tabsContainer = this.getTabs();

        this.contents.forEach((c) => content.appendChild(c));

        wrapper.appendChild(tabsContainer);
        wrapper.appendChild(content);
        this.appendChild(wrapper);
    }

}



