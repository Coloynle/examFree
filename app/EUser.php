<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EUser extends Model
{
    protected $table = 'e_users';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
