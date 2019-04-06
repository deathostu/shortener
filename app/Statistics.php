<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Statistics extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id', 'counter',
    ];
}
