<?php

namespace Xcholars\Auth\Traits;


trait GuardHelpers
{
   /**
    * The user provider implementation.
    *
    * @var object \Xcholars\Auth\UserProvider
    */
    private $provider;

   /**
    * The currently authenticated user.
    *
    * @var object Xcholars\Database\Orm\Model
    */
    private $user;

    /**
    * Get the ID for the currently authenticated user.
    *
    * @return int|string|null
    */
    public function id()
    {
        if ($this->loggedOut)
        {
            return;
        }

        return $this->user
                    ? $this->user->{$this->user->getKeyName()}
                    : $this->getSession();
    }

   /**
    * Determine if the current user is authenticated.
    *
    * @return bool
    */
    public function check()
    {
        return ! is_null($this->user());
    }

   /**
    * Determine if the current user is a guest.
    *
    * @return bool
    */
    public function guest()
    {
        return ! $this->check();
    }

}
