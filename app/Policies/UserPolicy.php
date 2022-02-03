<?php

Namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
    * check if the user can view the given
    *
    * @return bool;
    */
    public function show(User $auth, User $user)
    {
       return $auth->id === $user->id;
    }

    /**
    * check if the user can create new
    *
    * @return bool;
    */
    public function create(User $auth, User $user)
    {

    }

    /**
    * check if the user can update the given
    *
    * @return bool;
    */
    public function update(User $auth, User $user)
    {

    }


    /**
    * check if the user can delete the given
    *
    * @return bool;
    */
    public function delete(User $auth, User $user)
    {

    }
}
