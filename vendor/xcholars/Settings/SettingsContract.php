<?php

Namespace Xcholars\Settings;

interface SettingsContract
{
  /**
   * loop through config files & store all configs in an array
   *
   * @param array $file
   * @return void
   */
   public function set($files);


  /**
   * pull config for the given array key.
   *
   * @param string $key
   * @return array|string
   */
   public function get($key);
}
