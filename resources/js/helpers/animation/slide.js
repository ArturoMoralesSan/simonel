import { easeInOutQuad } from './easings.js';
import getComputedStyles from '../getComputedStyles.js';


/**
 * Display or collapse the given element with an animation.
 *
 * @param {HTMLElement} el
 * @param {number} duration
 * @param {string} direction
 */
export function slide(el, duration = 200, direction = 'down') {
    let computedStyle;
    let start;
    let originalStyles;
    let style;

    if (el.hasAttribute('data-slide-animation-styles')) {
        originalStyles = el.getAttribute('data-slide-animation-styles');
        computedStyle  = getComputedStyles(el, originalStyles);
    }
    else {
        const styleAttribute = el.getAttribute('style') || '';

        computedStyle  = getComputedStyles(el, styleAttribute);
        originalStyles = styleAttribute ? styleAttribute.replace(/(display(\s)?:)(\s)?(\w)+;/gi, '') : '';

        el.setAttribute('data-slide-animation-styles', originalStyles);
    }

    const height        = computedStyle.clientHeight ? computedStyle.clientHeight : el.clientHeight;
    const marginBottom  = parseInt(computedStyle.marginBottom.replace('px', ''), 10);
    const marginTop     = parseInt(computedStyle.marginTop.replace('px', ''), 10);
    const paddingBottom = parseInt(computedStyle.paddingBottom.replace('px', ''), 10);
    const paddingTop    = parseInt(computedStyle.paddingTop.replace('px', ''), 10);


    /**
     * Step in animation.
     *
     * @param {number} timestamp
     */
    const step = (timestamp) => {
        start = start || timestamp;
        const lapsed = timestamp - start;
        const easing = easeInOutQuad(lapsed, 0, 1, duration);
        const factor = (direction === 'down') ? easing : 1 - easing;

        if (lapsed < duration) {
            const currentHeight        = factor * height;
            const currentMarginBottom  = factor * marginBottom;
            const currentMarginTop     = factor * marginTop;
            const currentPaddingBottom = factor * paddingBottom;
            const currentPaddingTop    = factor * paddingTop;

            style = `${originalStyles} display: block; overflow: hidden; height: ${currentHeight}px; margin-bottom: ${currentMarginBottom}px; margin-top: ${currentMarginTop}px; padding-bottom: ${currentPaddingBottom}px; padding-top: ${currentPaddingTop}px;`;

            el.setAttribute('style', style);

            requestAnimationFrame(step);
        }
        else {
            const display = (direction === 'down') ? 'display: block;' : 'display: none;';
            style = originalStyles + ' ' + display;

            el.setAttribute('style', style);
            el.removeAttribute('data-slide-animation-styles');
        }
    };

    requestAnimationFrame(step);
}


/**
 * Slide down animation.
 *
 * @param {HTMLElement} el
 * @param {number} duration
 */
export function slideDown(el, duration = 200) {
    slide(el, duration, 'down');
}


/**
 * Slide up animation.
 *
 * @param {HTMLElement} el
 * @param {number} duration
 */
export function slideUp(el, duration = 200) {
    slide(el, duration, 'up');
}


/**
 * Display or collapse the given element with an animation depending on its
 * current state.
 *
 * @param {HTMLElement} el
 * @param {number} duration
 */
export function slideToggle(el, duration = 200) {
    const isHidden = getComputedStyles(el).display === 'none';

    if (isHidden) {
        slideDown(el, duration);
    }
    else {
        slideUp(el, duration);
    }
}
