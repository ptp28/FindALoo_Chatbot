<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Authenticatable;

class Logins extends Model implements \Illuminate\Contracts\Auth\Authenticatable {
    use Authenticatable;
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'login';

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}