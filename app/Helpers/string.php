<?php

use Illuminate\Support\Str;

/**
 * Convert multiple <br> tags into a paragraph tags.
 * Example: Foo<br>Bar<br><br>Baz => Foo<br>Bar</p><p>Baz
 *
 * @param  string $value
 * @return string
 */
function brsToParagraph($string) {
    $regex = '/(<br\s*\/?>\s*){2,}/';

    return preg_replace($regex, '</p><p>', nl2br($string));
}


/**
 * Insert paragraphs to textarea content.
 *
 * @param  string $value
 * @param  boolean $wrap
 * @return string
 */
function textareaToParagraphs($value, $wrap = true) {
    $value = trim($value);
    $value = preg_replace('/\r?\n/', '<br>', $value);
    $value = brsToParagraph($value);

    return ($wrap && $value) ? '<p>' . $value . '</p>' : $value;
}