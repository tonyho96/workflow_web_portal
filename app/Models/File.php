<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{

    protected $fillable = ['id', 'file_name', 'form_id'];

    protected $table = 'files';

    public function form() {
    	return $this->belongsTo('App\Models\Form', 'form_id', 'id');
    }
}
