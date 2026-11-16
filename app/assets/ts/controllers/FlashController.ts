import { AbstractController } from '../common/attraction/AbstractController';
import { $$ } from '../common/utils/dom';

export default class FlashController extends AbstractController {
  declare readonly element: HTMLElement;
  private autoRemoveDelay = 5000;
  private removeAnimationDuration = 400;

  connect() {
    this.initializeFlashes();
  }

  private initializeFlashes() {
    const flashes = $$('.flash', this.element);
    
    flashes.forEach((flash) => {
      
        setTimeout(() => this.removeFlash(flash as HTMLElement), this.autoRemoveDelay);
    });
  }

  removeByClick(event: Event) {
    const flash = (event.currentTarget as HTMLElement).closest('.flash') as HTMLElement;
    if (flash) {
      this.removeFlash(flash);
    }
  }

  private removeFlash(flash: HTMLElement) {
    // Add removing class to trigger exit animation
    flash.classList.add('removing');
    
    // Wait for animation to complete before removing from DOM
    setTimeout(() => {
      flash.remove();
    }, this.removeAnimationDuration);
  }
}
