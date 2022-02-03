<?php

Namespace Xcholars\Auth;

use Xcholars\Database\Orm\Model;

use Xcholars\Auth\Traits\GuardHelpers;

use Xcholars\Support\Traits\HasNestedArray;

use Xcholars\Http\Session\Manager as Session;

class SessionGuard
{
    use GuardHelpers;

    use HasNestedArray;

   /**
    * The name of the Guard. Typically "session".
    *
    * Corresponds to guard name in authentication configuration.
    *
    * @var string
    */
    private $name;

   /**
    * Indicates if the logout method has been called.
    *
    * @var bool
    */
    private $loggedOut = false;

   /**
    * Session insatnce
    *
    * @var object Xcholars\Sesssion\Manager
    */
    private $session;

   /**
    * Create a new authentication guard.
    *
    * @param  string  $name
    * @param object  Xcholars\Auth\UserProviderContract  $provider
    * @return void
    */
    public function __construct($name, UserProviderContract $provider, Session $session)
    {
        $this->name = $name;

        $this->provider = $provider;

        $this->session = $session;

    }

   /**
    * Attempt to authenticate a user using the given credentials.
    *
    * @param  array  $credentials
    * @return bool
    */
    public function attemptWith(array $credentials = [])
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($user === false)
        {
            return false;
        }

        if ($this->hasValidCredentials($user, $credentials))
        {
            $this->login($user);

            return true;
        }

        return false;
    }

  /**
    * Log a user into the application.
    *
    * @param object Xcholars\Database\Orm\Model $user
    * @return void
    */
    public function login(Model $user)
    {
        $this->updateSession(
            $user->getKeyName(), $user->{$user->getKeyName()}
        );

        $this->setUser($user);
    }

  /**
    * Log a user into the application.
    *
    * @return void
    */
    public function logout()
    {
        $this->session->forget('user');

        $this->loggedOut = true;
    }

   /**
    * Set the current user.
    *
    * @param object Xcholars\Database\Orm\Model $user
    * @return $this
    */
    public function setUser(Model $user)
    {
        $this->user = $user;

        $this->loggedOut = false;

        return $this;
    }

    /**
    * Update the session with the given ID.
    *
    * @param  string  $id
    * @return void
    */
    protected function updateSession($id, $value)
    {
        $this->session->set('user', [$id => $value]);
    }

   /**
    * Determine if the user matches the credentials.
    *
    * @param  mixed  $user
    * @param  array  $credentials
    * @return bool
    */
    private function hasValidCredentials($user, $credentials)
    {
        return ! is_null($user)
               && $this->provider->validateCredentials($user, $credentials);
    }

   /**
    * Get the currently authenticated user.
    *
    * @return object Xcholars\Database\Orm\Model
    */
    public function user()
    {
        if ($this->loggedOut)
        {
            return;
        }

        if (!is_null($this->user))
        {
            return $this->user;
        }

        if ($id = $this->getSession())
        {
            $this->user = $this->provider->retrieveById($id);

            $this->login($this->user);
        }

        return $this->user;
    }

   /**
    * Get the currently authenticated  user session .
    *
    * @return mixed
    */
    public function getSession()
    {
        return $this->get($this->session->get('user'), 'id');
    }
}
