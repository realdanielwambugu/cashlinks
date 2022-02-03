<?php

Namespace App\Policies;

use App\Models\User;

use App\Models\Thread;

class ThreadPolicy
{
    /**
    * check if the user can view the given thread
    *
    * @return bool;
    */
    public function show(User $user, Thread $thread)
    {

    }

    /**
    * check if the user can create new thread
    *
    * @return bool;
    */
    public function create(User $user, Thread $thread)
    {

    }

    /**
    * check if the user can update the given thread
    *
    * @return bool;
    */
    public function update(User $user, Thread $thread)
    {

    }


    /**
    * check if the user can delete the given thread
    *
    * @return bool;
    */
    public function delete(User $user, Thread $thread)
    {
        return $user->id == $thread->user->id;
    }
}
