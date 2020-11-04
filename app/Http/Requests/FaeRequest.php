<?php

namespace App\Http\Requests;

use App\Models\Attachement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request as FacadesRequest;

class FaeRequest extends FormRequest
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
        if (FacadesRequest::segment(3) == 'create') {
            return [
                'division_id' => 'required',
                'numero_of' => 'required',
                'code_affaire' => 'required',
                'client' => 'required',
                'montant' => 'required',
                'montant_devise' => 'required',
                'fae' => 'required|mimes:pdf',
            ];
        }else{ //validation for Update
            $ordre_file = Attachement::where([
                ['context', '=', FacadesRequest::segment(2)],
                ['ordre_id', '=', FacadesRequest::segment(3)]
            ])->get();
            return [
                'division_id' => 'required',
                'code_affaire' => 'required',
                'client' => 'required',
                'montant' => 'required',
                'montant_devise' => 'required',
                'fae' => 'required_with:ordre_file_id|mimes:pdf',
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
            'fae' => 'Facture à établir',
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
            'fae.required_with' => 'Salam'
        ];
    }
}
