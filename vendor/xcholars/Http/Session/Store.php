<?php

Namespace Xcholars\Http\Session;

use Xcholars\Http\Collections\InputCollection;

class Store extends InputCollection
{
    public function getSessions()
    {
        foreach ($_SESSION as $key => $value)
        {
            $this->parameters[$key] = $value;
        }
    }

    public function setSessions()
    {
        foreach ($this->parameters as $key => $value)
        {
            $_SESSION[$key] = $value;
        }
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);

        Parent::remove($key);
    }
}
