
/// <reference types="vite/client" />
import type { AbstractController } from './AbstractController';

type ControllerConstructor = new (element: HTMLElement) => AbstractController;

const CONTROLLER_NAME = /^(?:[A-Z][a-z]+)+Controller$/;

const instances = new WeakMap<HTMLElement, Map<string, AbstractController>>();
const pending = new WeakMap<HTMLElement, Set<string>>();
const observers = new WeakMap<Node, MutationObserver>();

function controllerNames(element: HTMLElement): string[] {
    return element.dataset.controller?.trim().split(/\s+/).filter(Boolean) ?? [];
}

function disconnectControllers(element: HTMLElement, keep = new Set<string>()): void {
    const elementInstances = instances.get(element);

    if (!elementInstances) {
        return;
    }

    for (const [name, controller] of elementInstances) {
        if (!keep.has(name)) {
            elementInstances.delete(name);
            controller.disconnectedCallback();
        }
    }

    if (elementInstances.size === 0) {
        instances.delete(element);
    }
}

async function connectControllers(
    element: HTMLElement,
    root: Node,
    controllersPath: string,
): Promise<void> {
    const names = controllerNames(element);
    let elementPending = pending.get(element);

    for (const name of names) {
        if (instances.get(element)?.has(name) || elementPending?.has(name)) {
            continue;
        }

        if (!CONTROLLER_NAME.test(name)) {
            continue;
        }

        if (!elementPending) {
            elementPending = new Set();
            pending.set(element, elementPending);
        }
        elementPending.add(name);

        try {
            const extension = import.meta.env.DEV ? 'ts' : 'js';
            const controllerUrl = new URL(`${controllersPath}/${name}.${extension}`, import.meta.url);
            let Controller: ControllerConstructor;

            try {
                ({ default: Controller } = await import(/* @vite-ignore */ controllerUrl.href) as {
                    default: ControllerConstructor;
                });
            } catch (error) {
                console.warn(`Contrôleur introuvable : ${name}`, error);
                continue;
            }

            if (!root.contains(element) || !controllerNames(element).includes(name)) {
                continue;
            }

            const controller = new Controller(element);
            const elementInstances = instances.get(element) ?? new Map<string, AbstractController>();
            elementInstances.set(name, controller);
            instances.set(element, elementInstances);
            controller.connectedCallback();
        } finally {
            elementPending.delete(name);
            if (elementPending.size === 0) {
                pending.delete(element);
            }
        }
    }
}

function findControllerElements(root: ParentNode): HTMLElement[] {
    return [
        ...(root instanceof HTMLElement && root.matches('[data-controller]') ? [root] : []),
        ...root.querySelectorAll<HTMLElement>('[data-controller]'),
    ];
}

function disconnectTree(node: Node, root: Node): void {
    if (node instanceof HTMLElement) {
        if (!root.contains(node)) {
            disconnectControllers(node);
        } else {
            disconnectControllers(node, new Set(controllerNames(node)));
        }
    }

    if (!(node instanceof Element)) {
        return;
    }

    node.querySelectorAll<HTMLElement>('[data-controller]').forEach((element) => {
        if (!root.contains(element)) {
            disconnectControllers(element);
        }
    });
}

async function initControllers(options: { root?: ParentNode; controllersPath?: string } = {}): Promise<void> {
    const opts = {
        root: document,
        controllersPath: import.meta.env.DEV ? '../../controllers' : '../controllers',
        ...options,
    };
    const rootNode = opts.root;

    if (!observers.has(rootNode)) {
        const observer = new MutationObserver((mutations) => {
            for (const mutation of mutations) {
                if (mutation.type === 'attributes' && mutation.target instanceof HTMLElement) {
                    const element = mutation.target;
                    disconnectControllers(element, new Set(controllerNames(element)));
                    void connectControllers(element, rootNode, opts.controllersPath);
                    continue;
                }

                for (const node of mutation.removedNodes) {
                    disconnectTree(node, rootNode);
                }

                for (const node of mutation.addedNodes) {
                    if (node instanceof HTMLElement) {
                        for (const element of findControllerElements(node)) {
                            void connectControllers(element, rootNode, opts.controllersPath);
                        }
                    }
                }
            }
        });

        observer.observe(rootNode, {
            attributes: true,
            attributeFilter: ['data-controller'],
            childList: true,
            subtree: true,
        });
        observers.set(rootNode, observer);
    }

    for (const element of findControllerElements(rootNode)) {
        disconnectControllers(element, new Set(controllerNames(element)));
        await connectControllers(element, rootNode, opts.controllersPath);
    }
}

export { initControllers };