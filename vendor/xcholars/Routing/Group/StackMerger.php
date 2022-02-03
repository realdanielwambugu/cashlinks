<?php

Namespace Xcholars\Routing\Group;

class StackMerger
{
    /**
 	* Merge route groups attributes into a new array.
 	*
 	* @return array
 	*/
 	public function merge(array $attributes, $groupStack)
 	{
 	    return array_merge($groupStack, [
                'middlware' => $this->mergeMiddleware($attributes, $groupStack),
     	        'namespace' => $this->mergeNamespace($attributes, $groupStack),
     	        'prefix' => $this->mergePrefix($attributes, $groupStack),
     		    'name' => $this->mergeName($attributes, $groupStack),
     		    ]);
 	}

    /**
 	* merge old and new route middlware
 	*
 	* @return string|null
 	*/
 	private function mergeMiddleware($new, $old)
 	{
        return array_merge($new['middlware'] ?? [], $old['middlware']);
 	}

   /**
 	* Merge the givent attribute with last group attributes
 	*
 	* @return array
 	*/
 	public function mergeWithLastGroup(array $attributes, $groupStack)
 	{
 	    return $this->merge($attributes, end($groupStack));
 	}

   /**
 	* merge new and old namespace
 	*
 	* @return string|null
 	*/
 	private function mergeNamespace($new, $old)
 	{
 	    if (isset($new['namespace']))
	    {
            return isset($old['namespace'])
                   ? $old['namespace'] . '\\' . $new['namespace']
                   : $new['namespace'];
 	    }

	    return $old['namespace'] ?? null;
 	}

   /**
	* merge new and old uri prefix
	*
	* @return string|null
	*/
	private function mergePrefix($new, $old)
	{
        if (isset($new['prefix']))
        {
            return isset($old['prefix'])
                   ? $old['prefix'] . '/' . $new['prefix']
                   : $new['prefix'];
        }

        return $old['prefix'] ?? null;
	}

   /**
	* merge old and new route name prefix
	*
	* @return string|null
	*/
	private function mergeName($new, $old)
	{
        $old = $old['name'] ?? null;

        $new = $new['name'] ?? null;

        return $old . $new;
	}

}
