<?php

Namespace Xcholars\Settings;

use Xcholars\Support\Proxies\Str;

class Settings implements SettingsContract
{
  /**
   * config .
   *
   * @var array
   */
   private $config = [];

  /**
   * pass directory containing application settings files.
   *
   * @param array $file
   * @return void
   */
   public function __construct($directory)
   {
       $this->set($directory);
   }

  /**
   * loop through config files & store all configs in an array
   *
   * @param array $file
   * @return void
   */
   public function set($directory)
   {
       $files = glob($directory);

       foreach ($files as $file)
       {
           if (is_array($config = require_once $file))
           {
               $this->config =  array_merge_recursive($this->config, $config);
           }
       }
   }


  /**
   * pull config for the given array key.
   *
   * @param string $key
   * @param string $default
   * @return array|string
   */
   public function get($key, $default = null)
   {
        $segments = explode('.', $key);

        $config = $this->config;

        foreach ($segments as $segment)
        {
            if (isset($config[$segment]))
            {
               $config = $config[$segment];
            }
            else
            {
                $config = $default;

                break;
            }
        }

        return $config;
   }


}
