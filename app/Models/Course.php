<?php

namespace App\Models;

use App\Models\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? Storage::url($this->thumbnail) : asset('images/default-thumbnail.jpg');
    }
}
