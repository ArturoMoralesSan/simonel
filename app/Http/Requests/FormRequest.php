<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;
use Illuminate\Support\Facades\Validator;

class FormRequest extends LaravelFormRequest
{
    /**
     * Create a new FormRequest instance.
     */
    public function __construct() {
        parent::__construct();

        $this->registerRequiredOnCreationRule();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Custom validation rules
    |--------------------------------------------------------------------------
    */

    /**
     * An attribute is required only when creating a resource (on POST request).
     *
     * @return void
     */
    protected function registerRequiredOnCreationRule()
    {
        Validator::extendImplicit('required_at_create',
            function($attribute, $value, $parameters, $validator) {
                if (! $this->isMethod('POST')) {
                    return true;
                }

                $v = Validator::make(request()->only($attribute), [
                    $attribute => 'required'
                ]);

                return $v->passes();
            }
        );
    }
}
