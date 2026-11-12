import { AbstractController } from '../common/attraction/AbstractController';


export default class EditorController extends AbstractController {
    editor!: HTMLElement

    display() 
    {
        this.editor.setAttribute("shown", "true")
    }

    connect() 
    {
        this.editor = this.element.nextElementSibling as HTMLElement;
    }

}