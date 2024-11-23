<?php

namespace App\Http\Resources;

use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="PublisherResource",
 *     type="object",
 *     title="Publisher Resource",
 *     description="Scheme for a Publisher",
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
 *
 * @mixin Publisher
 */
class PublisherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'website' => $this->website,
            'address' => $this->address,
            'zipcode' => $this->zipcode,
            'city' => $this->city,
            'country' => $this->country,
            'phone' => $this->phone,
        ];
    }
}
