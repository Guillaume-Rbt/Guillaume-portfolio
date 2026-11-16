// @ts-ignore
import '../css/common.css';
// @ts-ignore
import 'virtual:uno.css'

import {Tabs} from "./common/CustomElements/Tabs"
import { initControllers } from "./common/attraction/attraction"

import "visual-page-editor/style.css";
import { Text, VisualEditor } from 'visual-page-editor';
customElements.define('language-tabs', Tabs);
import { initFilesManager } from "./common/files-manager";

void initControllers();
void initFilesManager();

const projectEditor = new VisualEditor({
    allowAdd: false, 
    requiredBlocks: ['hero-project'], 
    reorderable: false
});

projectEditor.registerBlock({
    name: 'hero-project',
    label: 'Hero Project',
    fields: [
        Text("title", {
            
            label: 'My Text'
        })
    ]
});

projectEditor.defineElement("project-editor")