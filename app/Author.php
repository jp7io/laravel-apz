<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = ['name', 'email'];

    public function articles()
    {
        return $this->hasMany('App\Article');
    }
}
