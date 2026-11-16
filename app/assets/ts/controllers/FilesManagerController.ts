import { AbstractController } from '../common/attraction/AbstractController';
import { $, createElement } from '../common/utils/dom'

import type { FilesManagerElement } from "files-manager";
import { events } from '../common/utils/events';
export default class FilesManagerController extends AbstractController {

    inputElement: HTMLInputElement | null = $('input', this.element.parentElement!) as HTMLInputElement | null;
    fileManagerElement: FilesManagerElement | null = $('files-manager') as FilesManagerElement | null;
    promise: Promise<string | null> | null = null;
    deleteButton: HTMLButtonElement = createElement('button', '', 'btn', 'btn-danger', 'absolute', 'top-0', 'right-0', 'z-999', "p-1!") as HTMLButtonElement;


    set filePath(value: string | null) {

        this.inputElement!.setAttribute('value', value || '');
        this.element.style.backgroundImage = `url(${value})`;
        if (value !== "") {
            this.element.style.setProperty('--file-url', `url(${value})`);
            this.deleteButton.classList.remove('hidden!');
        }
        else {
            this.deleteButton.classList.add('hidden!');
            this.element.style.removeProperty('--file-url');
        }
    }

    get filePath() {
        return this.inputElement?.value || null;
    }


    openFileManager() {
        this.promise = this.fileManagerElement?.open({
            filters: { type: 'image' }
        }) || null;
        this.promise?.then((filePath) => {

            this.filePath = filePath ?? "";

        });
    }



    connect() {
        if (!this.inputElement) {
            console.warn('Input element not found');

        };
        if (!this.fileManagerElement) {
            console.warn('File manager element not found');
            return;
        }


        this.filePath = this.inputElement!.value;

        this.inputElement?.setAttribute('placeholder', 'Select a file');

        const icon = createElement('i', "", "icon", "icon-trash")
        this.deleteButton.appendChild(icon);
        this.element.appendChild(this.deleteButton);
        this.deleteButton.setAttribute('type', 'button');
        this.deleteButton.addEventListener(events.CLICK_TOUCH, this.onDelete.bind(this));
    }


    disconnect() {
        this.deleteButton.removeEventListener(events.CLICK_TOUCH, this.onDelete.bind(this));
    }


    onDelete(e: Event) {
        e.stopPropagation();
        this.filePath = "";
    }



}