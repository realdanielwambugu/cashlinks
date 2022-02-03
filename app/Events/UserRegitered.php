<?php

namespace App\Events;

use App\Models\User;

class UserRegitered
{

    public $user;

   /**
    * Create a new event instance.
    *
    * @param object App\Models\User  $user
    * @return void
    */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
