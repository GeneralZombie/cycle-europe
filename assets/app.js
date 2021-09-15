/*
 * Welcome to your app's main-2016 JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.sass in this case)
import './styles/app.sass';

// start the Stimulus application
import './bootstrap';

function isTouchEnabled() {
    return ('ontouchstart' in window) ||
        (navigator.maxTouchPoints > 0) ||
        (navigator.msMaxTouchPoints > 0);
}

if (isTouchEnabled) {
    document.body.classList.add('touch-device');
}