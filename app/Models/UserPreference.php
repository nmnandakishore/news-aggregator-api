<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = ['preference_name', 'value', 'user_id'];

    public const array PREFERENCES = ['sources', 'categories', 'authors'];

    public static array $createRules = [
        '*.name' => 'required|in:sources,categories,authors',
        '*.value.*' => 'required|string|max:255'
    ];


}

