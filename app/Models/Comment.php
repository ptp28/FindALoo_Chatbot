<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    protected $table = 'comment';
    protected $fillable = ['toilet_id',
							'comment',
							'user_id'];

	// public function export()
 //    {
 //        return $this->hasOne('App\Models\Logins','id','q_id');
 //    }

}
