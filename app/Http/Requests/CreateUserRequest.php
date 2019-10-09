<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|max:20|min:2',
            'last_name'  => 'max:20|min:2',
            'email'      => 'required|email|unique:clients',
            'phone'      => 'required|regex:/^[0-9]{10}$/|unique:clients',
            'password'   => 'required_with:email|min:8|regex:/^(?!.*\s)(?=(?:\P{L}*\p{L}){2})\S.{0,40}\S$/'
        ];
    }
}