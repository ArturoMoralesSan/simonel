<?php

namespace App\Services;
use Illuminate\Support\Arr;

class Versioning
{
    /**
     * Path to the file that contains the versioning map inside the resources folder.
     *
     * @var string
     */
    protected static $filePath = 'versioning.php';


    /**
     * Return a versioned path for the given asset.
     *
     * It uses the value stored at {extension}.{filename} by default.
     * This can be overridden by providing a specific key to search for.
     *
     * @param  string $filepath - Relative asset path (with file name and extension).
     * @param  string $searchKey - Key to search for instead of the default.
     * @return string
     */
    static function asset($filepath, $searchKey = '')
    {
        extract(pathinfo($filepath));

        $key = $searchKey ?: $extension . '.' . preg_replace('/\.min$/', '', $filename);
        $version = static::getVersion($key);

        $versionedFile = static::file($filepath, $version);

        return asset($versionedFile);
    }


    /**
     * Return a versioned filename (e.g. path/to/main.3.css).
     *
     * By default, the version part is only included when the value is greater than 1.
     * Set the $force parameter to true to always include it regardless of its value.
     *
     * @param  string  $filepath - Relative asset path (with file name and extension).
     * @param  integer $version
     * @param  boolean $force - Include the version number in the filename, regardless of its value.
     * @return string
     */
    static function file($filepath, $version, $force = false)
    {
        if (! $force && $version < 2) {
            return $filepath;
        }

        extract(pathinfo($filepath));

        $dirname = $dirname === '.' ? '' : $dirname;
        $versionedFilename = implode('.', [$filename, $version, $extension]);

        return implode('/', array_filter([$dirname, $versionedFilename]));
    }


    /**
     * Search and return a value from the versioning map.
     *
     * @param  string $key - Key to search for.
     * @return integer
     */
    static protected function getVersion($key)
    {
        $mapAbsolutePath = realpath(resource_path(static::$filePath));

        if (! $mapAbsolutePath) {
            throw new \Exception("Versioning map not found in '/resources/" . static::$filePath . "'.", 500);
        }

        $mapArray = include $mapAbsolutePath;

        if (! is_array($mapArray)) {
            throw new \Exception("The versioning file at '/resources/" . static::$filePath . "' is not returning a valid Array.", 500);
        }

        $map = Arr::dot($mapArray);

        if (! array_key_exists($key, $map)) {
            throw new \Exception("The asset '$key' was not found in the versioning file.", 500);
        }

        return $map[$key];
    }
}
