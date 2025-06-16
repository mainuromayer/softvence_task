<?php

namespace App\Models;

use App\Models\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'thumbnail'
    ];

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    public function contentsThroughModules()
    {
        return $this->hasManyThrough(Content::class, Module::class);
    }

}
