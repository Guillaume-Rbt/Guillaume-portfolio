import { $$, $ } from "../utils/dom";
import { events } from "../utils/events";

type ElementType<T extends HTMLElement = HTMLElement> = T;

export abstract class AbstractController<T extends HTMLElement = HTMLElement> {
    protected values?: Record<string, unknown>;

      getName(): string {
        return this.constructor.name.toLocaleLowerCase().replace('controller', '');
    }

      protected elementsAction = new Map<HTMLElement, { handler: EventListener, name: string }>();

    constructor(readonly element: ElementType<T>) {

        this.setTargets()
        this.setValues()
        this.addActions()
    }

    setValues() {
        if (!this.values) return;
        for (const [key, value] of Object.entries(this.values)) {
            const selector = `[data-${this.getName()}-${key}-value]`;
            const element = $(selector, this.element)
            if (element) {
                const v = element.getAttribute(`data-${this.getName()}-${key}-value`);
                (this as unknown as Record<string, unknown>)[`${key}Value`] = v || value;
            }
        }
    }
    setTargets() {
        const targets = $$(`[data-${this.getName()}-target]`, this.element )
        
        targets.forEach(target => {
            const name = target.getAttribute(`data-${this.getName()}-target`);
            if (!name) return;
           (this as unknown as Record<string, unknown>)[`${name}Target`] = target;
        })
        
    }

   addActions() {
    const elements = [...$$(`[data-${this.getName()}-action]`, this.element)]
    if(this.element.hasAttribute(`data-${this.getName()}-action`)) elements.push(this.element);
    elements.forEach(element => {
        const attr = element.getAttribute(`data-${this.getName()}-action`)?.split(/->|#/);
        const event = attr?.[0];
        const controller = attr?.[1].toLowerCase();
        const method = attr?.[2];
        if (!method || !event || !controller) return;
        if(controller !== this.getName()) return;
        this.elementsAction.set(element, { handler: (this as unknown as Record<string, EventListener>)[method].bind(this), name: event });
       
    });
   }
   
   connectedCallback(): void {
    this.connect();
    this.elementsAction.forEach(({ handler, name }, element) => {
        const eventName = (events as Record<string, string | undefined>)[name];
        if (!eventName)
            {
                console.warn(`Event "${name}" is not defined in events map, defaulting to 'click'`)
                return;
            }
        element.addEventListener(eventName ?? 'click', handler);
    });
   }

  disconnectedCallback(): void {
    this.disconnect();
    this.elementsAction.forEach(({ handler, name }, element) => {
        const eventName = (events as Record<string, string | undefined>)[name];
        if (!eventName) return;
        element.removeEventListener(eventName ?? 'click', handler);
    });
   }

    connect(): void {
    }

    disconnect(): void {
    }

}