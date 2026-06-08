/**
 * Escape the special characters in the given string
 * for its use in regular expressions.
 *
 * @param  {string} string
 * @return {string}
 */
export default function escapeForRegex(string) {
    return string.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
}
