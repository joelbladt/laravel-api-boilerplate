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
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $website
 * @property string|null $address
 * @property string|null $zipcode
 * @property string|null $city
 * @property string|null $country
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Book> $books
 * @property-read int|null $books_count
 * @method static \Database\Factories\PublisherFactory factory($count = null, $state = [])
 * @method static Builder<static>|Publisher newModelQuery()
 * @method static Builder<static>|Publisher newQuery()
 * @method static Builder<static>|Publisher query()
 * @method static Builder<static>|Publisher whereAddress($value)
 * @method static Builder<static>|Publisher whereCity($value)
 * @method static Builder<static>|Publisher whereCountry($value)
 * @method static Builder<static>|Publisher whereCreatedAt($value)
 * @method static Builder<static>|Publisher whereEmail($value)
 * @method static Builder<static>|Publisher whereId($value)
 * @method static Builder<static>|Publisher whereName($value)
 * @method static Builder<static>|Publisher wherePhone($value)
 * @method static Builder<static>|Publisher whereUpdatedAt($value)
 * @method static Builder<static>|Publisher whereWebsite($value)
 * @method static Builder<static>|Publisher whereZipcode($value)
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
