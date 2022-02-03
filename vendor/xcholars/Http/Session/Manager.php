<?php

Namespace Xcholars\Http\Session;

use RuntimeException;

class Manager
{
    private $store;

    private $started = false;

    public function __construct(array $session = [])
    {
        $this->store = new Store($session);
    }

    public function start()
    {
        if ($this->started)
        {
            return true;
        }

        if (PHP_SESSION_ACTIVE === session_status())
        {
            throw new RuntimeException(
                'Failed to start the session: already started by PHP.'
            );
        }

        if (headers_sent())
        {
            throw new RuntimeException(
                'Failed to start the session because headers have already
                been sent'
            );
        }

        if (!session_start())
        {
            throw new RuntimeException('Failed to start the session.');
        }

        $this->started = true;

        $this->store->getSessions();

    }

    public function started()
    {
        return $this->started;
    }

    public function has($key)
    {
        return $this->store->has($key);
    }

    public function get($key)
    {
        return $this->store->get($key);
    }

    public function set($key, $value)
    {
        return $this->store->set($key, $value);
    }

    public function forget($key)
    {
       $this->store->remove($key);
    }

    public function getStore()
    {
       return $this->store;
    }
}
