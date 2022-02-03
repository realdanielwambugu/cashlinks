<?php

Namespace Xcholars\Routing\Group;

class GroupStack
{
   /**
	* The route group attributes stack.
	*
	* @var array
	*/
	public $stack = [];

   /**
    * The route group StackMerger instance.
    *
    * @var object Xcholars\Routing\Group\StackMerger
    */
    private $merger;

   /**
	* Create new instance of GroupStack
	*
	* @return void
	*/
	public function __construct(StackMerger $merger)
	{
		$this->merger = $merger;
	}

   /**
 	* Check if the stack has any attributes
 	*
 	* @return bool
 	*/
 	public function isEmpty()
 	{
 	    return empty($this->stack);
 	}

   /**
	* update default group attributes in the stack
	*
	* @return void
	*/
	public function update(array $attributes)
	{
	    if (!$this->isEmpty())
		{
	    	$attributes = $this->merger->mergeWithLastGroup($attributes, $this->stack);
	    }

		$this->stack[] = $attributes;
	}

    /**
 	* Get the prefix for the last  group in the stack
 	*
    * @return string|null
 	*/
 	public function getLastGroupUriPrefix()
 	{
        if (!$this->isEmpty())
        {
            return end($this->stack)['prefix'] ?? null;
        }

        return null;
 	}

    /**
    * Get the namespace for the last  group in the stack
    *
    * @return string|null
    */
    public function getLastGroupNamespace()
    {
        if (!$this->isEmpty())
        {
            return end($this->stack)['namespace'] ?? null;
        }

        return null;
    }

    /**
    * Get the name prefix for the last  group in the stack
    *
    * @return string|null
    */
    public function getLastGroupNameprefix()
    {
        if (!$this->isEmpty())
        {
            return end($this->stack)['name'] ?? null;
        }

        return null;
    }

    /**
    * Get the middlwares for the last  group in the stack
    *
    * @return string|null
    */
    public function getLastGroupMiddlewares()
    {
        if (!$this->isEmpty())
        {
            return end($this->stack)['middlware'] ?? null;
        }

        return null;
    }
   /**
	* romove last group  attributes from the stack
	*
	* @return void
	*/
	public function removeLastGroup()
	{
        array_pop($this->stack);
	}

}
