<?php

Namespace Xcholars\Auth;

use Xcholars\Settings\SettingsContract;

class Hash
{
   /**
    * Create a new authentication guard.
    *
    * @param object Xcholars\Settings\SettingsContract
    * @return void
    */
    public function __construct(SettingsContract $settings)
    {
        $this->settings = $settings;
    }

   /**
    * Hash the password using the given algo & cost
    *
    * @param string $password
    * @return bool
    */
    public function make($password)
    {
        return password_hash(
               $password,
               $this->settings->get('auth.hash.algo'),
               ['cost' => $this->settings->get('auth.hash.cost')],
               );
    }

   /**
    * Hash the password using the given algo & cost
    *
    * @param string $password
    * @return bool
    */
    public function check($password, $hash)
    {
        return password_verify($password, $hash);
    }

}
