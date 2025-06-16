<?php

namespace App\Models;

use App\Models\Module;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'type',
        'content',
        'description',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
}
