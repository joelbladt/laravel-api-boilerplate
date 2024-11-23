<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="UpdatePublisherRequest",
 *     type="object",
 *     title="Update Publisher Request",
 *     description="RequestBody to update a Publisher",
 *
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the publisher",
 *         example="Bloomsbury Publishing Plc"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Contact e-mail of the publisher",
 *         example="contact@bloomsbury.com"
 *     ),
 *     @OA\Property(
 *         property="website",
 *         type="string",
 *         description="Website of the publisher",
 *         example="https://www.bloomsbury.com"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string",
 *         description="Address of the publisher (street and number)",
 *         example="50 Bedford Square"
 *     ),
 *     @OA\Property(
 *         property="zipcode",
 *         type="string",
 *         description="Zip code of the publishing house",
 *         example="WC1B 3DP"
 *     ),
 *     @OA\Property(
 *         property="city",
 *         type="string",
 *         description="City of the publishing house",
 *         example="London"
 *     ),
 *     @OA\Property(
 *         property="country",
 *         type="string",
 *         description="Country of the publishing house",
 *         example="United Kingdom"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Phone number of the publisher",
 *         example="+44 (0)20 7631 5600"
 *     )
 * )
 */
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
                'sometimes',
                'required',
                'string',
                'filled',
                'max:255',
                Rule::unique('publishers')
                    ->ignore($this->publisher)
                    ->where(function ($query) {
                        return $query->where('email', $this->email)
                            && $query->where('website', $this->website);
                    }),
            ],
            'email' => 'sometimes|required|string|email|filled',
            'website' => 'sometimes|required|string|url|filled',
            'address' => 'string|nullable',
            'zipcode' => 'string|nullable',
            'city' => 'string|nullable',
            'country' => 'string|nullable',
            'phone' => 'string|nullable',
        ];
    }
}
