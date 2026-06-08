<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class GalleryIds implements Rule
{
    /**
     * List of valid image IDs.
     *
     * @var array
     */
    protected $validIds;


    /**
     * Create a new rule instance.
     *
     * @param array $validIds
     * @param boolean $returnInvalidIndexes
     * @return void
     */
    public function __construct($validIds)
    {
        $this->validIds = $validIds;
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
        if ($value === 'file') {
            return true;
        }

        return in_array(intval($value), $this->validIds, true);
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El identificador (ID) de una o más imágenes es inválido.';
    }
}
