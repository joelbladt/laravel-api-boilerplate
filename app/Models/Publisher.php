<?php

namespace App\Models;

use Database\Factories\PublisherFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Publisher
 *
 * @property string $name
 * @property string $email
 * @property string $website
 * @property string|null $address
 * @property string|null $zipcode
 * @property string|null $city
 * @property string|null $country
 * @property string|null $phone
 * @property string|null $fax
 *
 * @method static PublisherFactory factory($count = null, $state = [])
 * @method static Builder|Publisher newModelQuery()
 * @method static Builder|Publisher newQuery()
 * @method static Builder|Publisher query()
 * @method static Builder|Publisher whereId($value)
 *
 * @mixin \Eloquent
 */
class Publisher extends Model
{
    /** @use HasFactory<PublisherFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'website',
        'address',
        'zipcode',
        'city',
        'country',
        'phone',
    ];

    /**
     * @return HasMany<Book, $this>
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
