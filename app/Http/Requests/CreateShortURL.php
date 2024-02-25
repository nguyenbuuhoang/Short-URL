<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateShortURL extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'url' => 'required|url'
        ];
    }
    public function messages(): array
    {
        return [
            'url.url' => 'Please enter the correct URL',
            'url.required' => 'The URL must not be left blank.',
        ];
    }
}
