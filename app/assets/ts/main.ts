// @ts-ignore
import '../css/common.css';
// @ts-ignore
import 'virtual:uno.css'
// @ts-ignore
import "visual-page-editor/style.css";
import { Text, VisualEditor } from 'visual-page-editor';


const editor = new VisualEditor();

editor.registerBlock({
    name: 'my-block',
    label: 'My Block',
    fields: [
        Text("title", {
            
            label: 'My Text'
        })
    ]
});

editor.defineElement("visual-editor")
