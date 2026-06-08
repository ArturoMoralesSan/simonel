/*
|-------------------------------------------------------------------------------
| Easing functions
|-------------------------------------------------------------------------------
*/

/**
 * Quadratic easing in/out.
 *
 * @param  {number} t - Current time in animation
 * @param  {number} b - Beggining/original value of property or percentage
 * @param  {number} c - Change in value: Destination value minus original value of property or percentage
 * @param  {number} d - Total duration of animation
 * @return {number}
 */
export function easeInOutQuad(t, b, c, d) {
    if ((t /= d / 2) < 1) {
        return c / 2 * t * t + b;
    }

    return -c / 2 * ((--t) * (t - 2) - 1) + b;
}


/**
 * Cubic easing in/out.
 *
 * @param  {number} t - Current time in animation
 * @param  {number} b - Beggining/original value of property or percentage
 * @param  {number} c - Change in value: Destination value minus original value of property or percentage
 * @param  {number} d - Total duration of animation
 * @return {number}
 */
export function easeInOutCubic(t, b, c, d) {
    if ((t /= d / 2) < 1) {
        return c / 2 * t * t * t + b;
    }

    return c / 2 * ((t -= 2) * t * t + 2) + b;
}
