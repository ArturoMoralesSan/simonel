<?php

use App\Services\Versioning;


/**
 * Check if the body element has any of the given CSS classes.
 *
 * @param  string $classes - Class name(s) separated by comma.
 * @return boolean
 */
function at($classes)
{
    $query = explode(',', $classes);
    $bodyClasses = explode(' ', view()->getSection('class', ''));

    return !! array_filter($query, function($classes) use ($bodyClasses) {
        return in_array(trim($classes), $bodyClasses);
    });
}


/**
 * Return a versioned path for the given asset.
 *
 * By default it uses the value stored at {extension}.{filename} in the resources versioning map.
 * This can be overridden by providing a specific key to search for.
 *
 * @param  string $filepath - Relative asset path (with file name and extension).
 * @param  string $searchKey - Key to search for instead of the default.
 * @return string
 */
function version($filepath, $searchKey = '')
{
    return Versioning::asset($filepath, $searchKey);
}
