import {FilesManager} from 'files-manager';

// @ts-ignore
import "files-manager/style.css";
let filesManager: FilesManager | null = null



function initFilesManager() {
    if (!filesManager) {
        filesManager = new FilesManager();
        filesManager.defineElement();
    }
    return filesManager;
}


export {initFilesManager};
