/**
 * Return the computed styles of the given hidden element.
 * The parent must be visible to work.
 *
 * @param {HTMLElement} el
 * @param {string|null} forceOriginalStyles - Take the given string as original styles,
 *     instead of getting them from the DOM.
 */
export default function getComputedStyles(el, forceOriginalStyles = null) {
    const originalStyles = forceOriginalStyles !== null ?
        forceOriginalStyles :
        (el.getAttribute('style') || '');

    const newStyles = originalStyles + 'visibility: hidden !important; display: block !important;';
    let computedStyles;

    el.setAttribute('style', newStyles);

    computedStyles = window.getComputedStyle(el);
    computedStyles.clientHeight = el.clientHeight;
    computedStyles.clientWidth = el.clientWidth;

    el.setAttribute('style', originalStyles);

    return computedStyles;
}
