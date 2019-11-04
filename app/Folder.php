<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\AppController;

class Folder extends Model
{
    protected $fillable = ['name', 'parent_id', 'is_deleted'];

    static public function folders()
    {
        $this->hasMany('Folder');
    }
    static public function query()
    {
        $allowed_ids = AppController::get_allowed_catalogs();
        $query = self::where(['is_deleted' => 0]);
        if (is_array($allowed_ids)) {
            $query->whereIn('id', $allowed_ids);
        }
        return $query;
    }
}
