<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['name', 'catalog_id'];

    public function folders()
    {
        return $this->belongsToMany('App\Folder', 'stocks_folders');
    }
}
