<?php

Namespace App\Models;

use Xcholars\Database\Orm\Model;

use Xcholars\Support\Proxies\Gate;

class User extends Model
{
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    public function can($ability, $aguments)
    { 
        return Gate::allows($ability, $aguments);
    }

}
