import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

import * as Turbo from "@hotwired/turbo";

Turbo.setProgressBarDelay(100);

document.addEventListener("turbo:before-render", (event) => {
    document.body.classList.add(
        "transition-opacity",
        "duration-500",
        "opacity-0"
    );
});

document.addEventListener("turbo:render", () => {
    document.body.classList.remove("opacity-0");
});
