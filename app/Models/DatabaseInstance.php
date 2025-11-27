<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseInstance extends Model
{
    protected $fillable = [
        'name','type','project','compose_path','env','status','last_message'
    ];

    protected $casts = [
        'env' => 'array',
    ];
}
