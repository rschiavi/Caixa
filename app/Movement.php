<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movement extends Model
{
    use SoftDeletes;

    public $dates = ['date'];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }
}
