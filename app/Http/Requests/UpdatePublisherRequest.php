<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePublisherRequest extends FormRequest
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
            'name' => [
                'string',
                'max:255',
                Rule::unique('publishers')
                    ->ignore($this->publisher)
                    ->where(function ($query) {
                        return $query->where('email', $this->email)
                            && $query->where('website', $this->website);
                    }),
            ],
            'email' => 'string|email',
            'website' => 'string|url',
            'address' => 'string|nullable',
            'zipcode' => 'string|nullable',
            'city' => 'string|nullable',
            'country' => 'string|nullable',
            'phone' => 'string|nullable',
        ];
    }
}
