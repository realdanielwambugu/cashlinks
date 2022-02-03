<?php

namespace Xcholars\Support\Traits;

use Xcholars\Support\Proxies\Str;

trait HasNestedArray
{
   /**
    * pull config for the given array key.
    *
    * @param string $key
    * @return array|string
    */
    public function get($array, $key, $default = null)
    {
        $segments = explode('.', $key);

        foreach ($segments as $segment)
        {
            if (isset($array[$segment]))
            {
               $array = $array[$segment];
            }
            else
            {
                $array = $default;

                break;
            }
        }

        return $array;
    }
}
