<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = ['name', 'type'];

    public function set_settings()
    {
        return $this->belongsToMany('App\PluginSetting', 'plugins_settings', 'plugin_id', 'name');
    }
    public function settings()
    {
        return $this->hasMany('App\PluginSetting');
    }
}
