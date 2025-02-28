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
 * @property string $title
 * @property string $author
 * @property string $isbn
 * @property string $publisher_id
 * @property string|null $publication_year
 * @property string|null $genres
 * @property string|null $summary
 *
 * @method static BookFactory factory($count = null, $state = [])
 * @method static Builder|Book newModelQuery()
 * @method static Builder|Book newQuery()
 * @method static Builder|Book query()
 * @method static Builder|Book whereId($value)
 *
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
