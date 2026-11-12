import { AbstractController } from '../common/attraction/AbstractController';
import { createElement, $$ } from '../common/utils/dom';
import { events } from '../common/utils/events';
export default class FlashController extends AbstractController {
  declare readonly element: HTMLElement;
  private autoRemoveDelay = 3500;
  private removeAnimationDuration = 400;

  connect() {
    this.initializeFlashes();
  }

  private initializeFlashes() {
    const flashes = $$('.flash', this.element);
    
    flashes.forEach((flash) => {
      
        setTimeout(() => this.removeFlash(flash as HTMLElement), this.autoRemoveDelay);

      // Add close button for manual dismissal
      this.addCloseButton(flash as HTMLElement);
    });
  }

  private addCloseButton(flash: HTMLElement) {
    const button = createElement('button', '', 'ml-auto', 'pl-4', 'text-current', 'opacity-70', 'hover:opacity-100', 'transition-opacity', 'cursor-pointer') as HTMLButtonElement;
    button.type = 'button';
    button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
  <path d="M0 0h24v24H0z" fill="none" />
  <path fill="currentColor" d="M18.3 5.71a.996.996 0 0 0-1.41 0L12 10.59L7.11 5.7A.996.996 0 1 0 5.7 7.11L10.59 12L5.7 16.89a.996.996 0 1 0 1.41 1.41L12 13.41l4.89 4.89a.996.996 0 1 0 1.41-1.41L13.41 12l4.89-4.89c.38-.38.38-1.02 0-1.4" />
</svg>`;
    button.setAttribute('aria-label', 'Fermer le message');

    const onCloseClick = () => {
      this.removeFlash(flash);
      button.removeEventListener(events.CLICK_TOUCH, onCloseClick.bind(this));
    }
    
    button.addEventListener(events.CLICK_TOUCH, onCloseClick.bind(this));
    
    flash.appendChild(button);
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
