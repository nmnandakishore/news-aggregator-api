<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    /**
     * @var array|string[]
     */
    protected array $rules = [
        'title' =>'required|text',
        'url' =>'required|text',
        'provider' =>'required|string',
        'content' =>'required|text',
        'category' => 'required|string',
        'description' =>'text',
        'image' =>'text',
        'tags' =>'string',
        'language' =>'string',
        'author' =>'string',
        'source' =>'string',
        'published_at' => 'string',
        'country' => 'string'
    ];

    /**
     * @var string[]
     */
    protected $fillable = ['title','description','image','url','tags','provider', 'author','content','category','source', 'published_at', 'country'];

   /* public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }*/

}
