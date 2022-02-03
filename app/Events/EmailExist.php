<?php

namespace App\Events;

use App\Models\User;

class EmailExist
{

    public $user;

   /**
    * Create a new event instance.
    *
    * @param object App\Models\User  $order
    * @return void
    */
    public function __construct(User $user)
    {  
        $this->user = $user;
    }
}
