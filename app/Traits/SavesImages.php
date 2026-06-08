<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Image;

trait SavesImages
{
    /**
     * Fetch the save settings for an uploaded image.
     *
     * @param  string $type
     * @return array
     */
    protected function getImageConfig($type)
    {
        $settings = [
            'artists' => [
                'width'  => 1024,
                'height' => null,
                'path'   => storage_path('app/public/artistas/'),
                'constraints' => ['aspectRatio']
            ]
        ];

        return $settings[$type] ?? $settings['default'];
    }


    /**
     * Store the uploaded image.
     *
     * @param  \Illuminate\Http\UploadedFile $image
     * @param  string $type
     * @param  string $filename
     * @return array
     */
    protected function saveImage($image, $type = 'default', $filename = '')
    {
        ini_set('memory_limit', '256M');

        $config = $this->getImageConfig($type);
        $pathInfo = pathinfo($image->getClientOriginalName());
        $filename = $filename ?: Str::slug($pathInfo['filename']);

        if (array_key_exists('filename', $config)) {
            $fullname = str_replace('{name}', $filename, $config['filename']) . '.' . $pathInfo['extension'];
        }
        else {
            $fullname = $filename . '.' . $pathInfo['extension'];
        }

        $savedImage = Image::make($image)
            ->resize($config['width'], $config['height'], function ($constraint) use($config) {
                if (key_exists('constraints', $config)) {
                    foreach ($config['constraints'] as $rule) {
                        $constraint->{$rule}();
                    }
                }
                else {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            })
            ->save($config['path'] . $fullname, 80);

        return [
            'filename' => $fullname,
            'width'    => $savedImage->width(),
            'height'   => $savedImage->height()
        ];
    }
}
