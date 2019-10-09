<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class ProductDescriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $request = $this->request;

        $rules = [
            'title'             => 'required|min:3|max:255',
            'short_description' => 'required|min:5',
            'description'       => 'required|min:5',
            'image'             => 'required',
            'h1'                => 'required|min:3|max:255',
            'meta_title'        => 'required|min:3|max:80',
            'meta_description'  => 'required|min:5|max:158',
            'quantity'          => 'required|integer',
            'minimum_quantity'  => 'required|integer|lte:quantity',
            'maximum_quantity'  => 'required|integer|lte:quantity',
            'volume_weight'     => 'integer',
            'category_id'       => 'required',
            'manufacturer_id'   => 'required',
        ];

        if($request->get('markdown'))
        {
            $rules['markdown_reason'] = 'required|min:2|max:255';
        }
        return $rules;
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [

        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
