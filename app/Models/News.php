<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    protected array $rules = [
        'title' =>'required|string',
        'description' =>'string',
        'image' =>'string',
        'url' =>'required|string',
        'tags' =>'string',
        'provider' =>'required',
        'language' =>'string',
        'author' =>'string',
        'content' =>'required|string'
    ];
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

}
