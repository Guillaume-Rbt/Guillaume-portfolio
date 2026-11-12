import { AbstractController } from '../common/attraction/AbstractController';
import { $ } from '../common/utils/dom';
import { events } from '../common/utils/events';

export default class TitleController extends AbstractController<HTMLInputElement> {
    target: HTMLElement | null = null;

    constructor(element: HTMLInputElement) {
        super(element);
        this.onTitleChange = this.onTitleChange.bind(this);
    }

    connect(): void {
        const parent = this.element.closest<HTMLElement>('[data-translation]');
        if (!parent) return;

        this.target = $('project-editor', parent);
        if (!this.target) return;

        this.element.addEventListener(events.CHANGE, this.onTitleChange);
        this.updatePreviewUrl();
    }

    onTitleChange(): void {
        this.updatePreviewUrl();
    }

    disconnect(): void {
        this.element.removeEventListener(events.CHANGE, this.onTitleChange);
        this.target = null;
    }

    private updatePreviewUrl(): void {
        const params = new URLSearchParams({ title: this.element.value });
        this.target?.setAttribute('previewUrl', `/preview?${params.toString()}`);
    }
}