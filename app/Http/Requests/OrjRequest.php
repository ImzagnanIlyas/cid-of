<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Ordre;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request as FacadesRequest;

class OrjRequest extends FormRequest
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
        $id = FacadesRequest::segment(3); //id of selected ordre
        $ordre = Ordre::findOrFail($id); //Ordre to be updated
        return [
            'division_id' => 'required',
            'code_affaire' => 'required',
            'client' => 'required',
            'montant' => 'required',
            'montant_devise' => 'required',
            'document' => 'required_with:ordre_file_id|mimes:pdf',
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
            'document.required_with' => 'Le document est requis si vous supprimez l\'ancien'
        ];
    }
}
