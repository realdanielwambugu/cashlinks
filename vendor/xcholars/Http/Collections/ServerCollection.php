<?php

Namespace Xcholars\Http\Collections;


class serverCollection extends InputCollection
{
   /**
    * Gets the HTTP headers from [$_SERVER]
    *
    * @return array
    */
    public function getHeaders()
    {
        $headers = [];

        foreach ($this->parameters as $key => $value)
        {
            if (0 === strpos($key, 'HTTP_'))
            {
                $headers[substr($key, 5)] = $value;
            }
        }

        return $headers;
    }
}
