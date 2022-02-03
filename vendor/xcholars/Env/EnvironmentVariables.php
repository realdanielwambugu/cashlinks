<?php

Namespace Xcholars\Env;

class EnvironmentVariables
{
    public function load($path)
    {
        if (file_exists($path  . DIRECTORY_SEPARATOR . '.env'))
        {
            return (\Dotenv\Dotenv::createImmutable($path))->load();
        }

        return false;
    }
}
