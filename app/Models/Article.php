<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'url',
        'source',
        'category',
        'author',
        'published_at',
    ];

    public function scopeFiltered( $query, $preferences )
    {
        if ( !empty($preferences['categories']) ) {
            $query->whereIn('category', $preferences['categories']);
        }

        if ( !empty($preferences['sources']) ) {
            $query->whereIn('source', $preferences['sources']);
        }

        if ( !empty($preferences['authors']) ) {
            $query->whereIn('author', $preferences['authors']);
        }

        return $query;
    }
}
