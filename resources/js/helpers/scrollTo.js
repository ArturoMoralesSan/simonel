import { easeInOutCubic } from './animation/easings.js';
import getScrollTop from './getScrollTop.js';


/**
 * Scroll to a given position or element using animation.
 *
 * @param {number|HTMLElement} target - Position in document (in pixels) or HTML element.
 * @param {number} offset             - Offset used as a breathing space.
 * @param {number} duration           - Duration (in milliseconds) of animation.
 */
export default function scrollTo(target, offset = 0, duration = 800) {
    const scrollTop = getScrollTop();
    let currentTime = 0;
    let start;
    let position;


    /**
     * Move forward in the animation.
     *
     * @param {number} timestamp
     */
    const step = (timestamp) => {
        if (! start) {
            start = timestamp;
        }

        const lapsed = timestamp - start;

        if (lapsed < duration) {
            const y = easeInOutCubic(lapsed, scrollTop, position - scrollTop, duration);

            window.scrollTo(0, y);

            requestAnimationFrame(step);
        }
        else {
            window.scrollTo(0, position);
        }
    };


    /**
     * Return the target's position relative to document.
     *
     * @return {number}
     */
    const calculateTargetPosition = () => {
        if (typeof target === 'number') {
            return target + offset;
        }

        return Math.round(target.getBoundingClientRect().top + scrollTop + offset);
    };


    position = calculateTargetPosition();

    requestAnimationFrame(step);
}
