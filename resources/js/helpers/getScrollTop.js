/**
 * Return the given element's top position relative to the document.
 * If no element is given, the position of the viewport is returned.
 *
 * @param {HTMLElement|null} el
 * @return {number}
 */
export default function getScrollTop(el = null) {
    const body = document.body;
    const docEl = document.documentElement;

    return Math.round(
        (el ? el.getBoundingClientRect().top : 0) +
        (window.pageYOffset || docEl.scrollTop || body.scrollTop) -
        (docEl.clientTop || body.clientTop || 0)
    );
}
