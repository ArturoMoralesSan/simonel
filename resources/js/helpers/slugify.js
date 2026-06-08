import { remove } from 'diacritics';


/**
 * Create a slug from the given string.
 *
 * @param  {string} string
 * @return {string}
 */
export default function slugify(string) {
    return remove(string.trim())
        .replace(/[/_\s]+/g, '-') // Replace underscores, slashes and spaces with "-"
        .replace(/[^\w|-]/g, '')  // Remove characters that are not letters, numbers, underscores or dashes
        .replace(/-{2,}/, '-')    // Replace multiple dashes with a single one
        .toLowerCase();
}
