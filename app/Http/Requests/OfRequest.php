<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Attachement;
use App\Models\Of;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request as FacadesRequest;

class OfRequest extends FormRequest
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
        //validation for Create
        if (FacadesRequest::segment(4) == 'create') {
            return [
                'division_id' => 'required',
                'ville' => 'required',
                'numero_of' => 'required',
                'code_affaire' => 'required',
                'client' => 'required',
                'montant' => 'required',
                'montant_devise' => 'required',
                'of' => 'required|mimes:pdf',
                'justification.*' => 'nullable|mimes:pdf',
            ];
        }else{ //validation for Update
            return [
                'division_id' => 'required',
                'ville' => 'required',
                'code_affaire' => 'required',
                'client' => 'required',
                'montant' => 'required',
                'montant_devise' => 'required',
                'of' => 'required_with:ordre_file_id|mimes:pdf',
                'justification.*' => 'nullable|mimes:pdf',
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
            'of' => 'Ordre de facturation',
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
            'of.required_with' => 'Salam'
        ];
    }
}
