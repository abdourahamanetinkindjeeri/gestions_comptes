<?php

namespace App\Http\Requests;

use App\Rules\NciSenegalRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    // app/Http/Requests/StoreCompteRequest.php
    public function rules()
    {
        return [
            'type' => 'required|in:cheque,epargne',
            'soldeInitial' => 'required|numeric|min:10000',
            'devise' => 'required|in:FCFA,USD,EUR',
            'client.titulaire' => 'required|string',
            'client.nci' => ['required', new NciSenegalRule()],
            'client.email' => 'required|email|unique:clients,email',
            'client.telephone' => ['required', 'unique:clients,telephone', new TelephoneSenegalRule()],
            'client.adresse' => 'required|string',
        ];
    }
}
