<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOfRequest extends FormRequest
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
        return [
            'division_id' => 'required',
            'numero_of' => 'required',
            'code_affaire' => 'required|unique:ordres,code_affaire',
            'client' => 'required',
            'montant' => 'required',
            'montant_devise' => 'required',
            'of' => 'required|mimes:pdf',
            'justification.*' => 'nullable|mimes:pdf',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'montant_devise' => 'devise',
            'of' => 'fichier de l\'ordre de facturation',
            'justification.*' => 'justification'
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
