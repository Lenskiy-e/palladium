<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
    public function rules() : array
    {

        // TODO прокомментировать!
        $rules = [];

        if($this->request->get('login_by') === 'email')
        {
            $rules = [
                'email'     => 'required|email',
            ];
        }

        if($this->request->get('login_by') === 'phone')
        {
            $rules = [
                'phone'     => 'required|min:12',
            ];
        }

        if($this->request->get('login_type') === 'permanent')
        {
            $rules = array_merge($rules, [
                'password'  => 'required'
            ]);
        }

        return $rules;
    }
}
