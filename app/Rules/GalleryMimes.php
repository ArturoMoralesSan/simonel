<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class GalleryMimes implements Rule
{
    /**
     * The accepted MIME types for the uploaded images.
     *
     * @var string
     */
    protected $mimeTypes;


    /**
     * Create a new rule instance.
     *
     * @param string $mimeTypes
     * @return void
     */
    public function __construct($mimeTypes = 'jpeg,png,gif')
    {
        $this->mimeTypes = $mimeTypes;
    }


    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Validator::make(
            ['image' => $value],
            ['image' => 'file|mimes:' . $this->mimeTypes]
        )->passes();
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $values = implode(', ', explode(',', $this->mimeTypes));

        return "Las imágenes deben ser archivos con formato: {$values}.";
    }
}
