const isMobile =
	typeof navigator !== "undefined" &&
	/(Android|iPhone|iPad|iPod|Mobile)/i.test(navigator.userAgent);


    export const Events = {
        click: isMobile ? "touchend" : "click"
    }