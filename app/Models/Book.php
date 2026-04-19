<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Book
 *
 * @property int $id
 * @property string $title
 * @property string $author
 * @property string|null $isbn
 * @property int|null $publisher_id
 * @property int|null $publication_year
 * @property string|null $genres
 * @property string|null $summary
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Publisher|null $publisher
 * @method static \Database\Factories\BookFactory factory($count = null, $state = [])
 * @method static Builder<static>|Book newModelQuery()
 * @method static Builder<static>|Book newQuery()
 * @method static Builder<static>|Book query()
 * @method static Builder<static>|Book whereAuthor($value)
 * @method static Builder<static>|Book whereCreatedAt($value)
 * @method static Builder<static>|Book whereGenres($value)
 * @method static Builder<static>|Book whereId($value)
 * @method static Builder<static>|Book whereIsbn($value)
 * @method static Builder<static>|Book wherePublicationYear($value)
 * @method static Builder<static>|Book wherePublisherId($value)
 * @method static Builder<static>|Book whereSummary($value)
 * @method static Builder<static>|Book whereTitle($value)
 * @method static Builder<static>|Book whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'publication_year',
        'genres',
        'summary',
    ];

    public function casts(): array
    {
        return [
            'publication_year' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Publisher, $this>
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }
}
