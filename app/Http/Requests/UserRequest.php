<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request as FacadesRequest;

class UserRequest extends FormRequest
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
        if (FacadesRequest::segment(4) == 'create') {
            return [
                'name' => 'required|min:3|max:50',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:8',
                'role' => 'required',
            ];
        }else {
            return [
                'name' => 'min:3|max:50',
                'email' => 'email',
                'password' => 'nullable|confirmed|min:8',
                //'role' => 'required',
            ];
        }

    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
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
