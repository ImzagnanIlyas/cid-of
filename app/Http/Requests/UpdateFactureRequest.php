<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFactureRequest extends FormRequest
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
        if( backpack_user()->role_id == config('backpack.role.su_id') ){
            return [
                'numero_facture' => 'required|unique:factures,numero_facture',
                'montant' => 'required',
                'montant_devise' => 'required',
            ];
        }else{
            return [
                'date_reception_client' => 'required',
                'reception_file' => 'required|mimes:pdf',
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
            'reception_file' => 'fichier de réception',
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
