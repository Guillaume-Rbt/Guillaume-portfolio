export function $(selector: string, context: Document | HTMLElement = document): HTMLElement | null {
    return context.querySelector(selector);
}

export function $$(selector: string, context: Document | HTMLElement = document): NodeListOf<HTMLElement> {
    return context.querySelectorAll(selector);
}

export function createElement(tag: string, id?: string, ...classes: string[]): HTMLElement {
    const element = document.createElement(tag);
    if (id) element.id = id;
    if (classes.length) element.classList.add(...classes);
    return element;
}

