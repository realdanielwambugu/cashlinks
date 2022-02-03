<?php

namespace Xcholars\Auth\Access;

use Xcholars\ApplicationContract;

use InvalidArgumentException;

class Gate implements GateContract
{
    /**
    * The container instance.
    *
    * @var object Xcholars\ApplicationContract  $app
    */
    private $app;

   /**
    * The user resolver callable.
    *
    * @var callable
    */
    private $userResolver;

   /**
    * All of the defined abilities.
    *
    * @var array
    */
    private $abilities = [];

   /**
    * All of the defined policies.
    *
    * @var array
    */
    private $policies = [];

   /**
    * Create a new gate instance.
    *
    * @param object Xcholars\ApplicationContract  $app
    * @param  callable  $userResolver
    * @return void
    */
    public function __construct(ApplicationContract $app, callable $userResolver)
    {
        $this->app = $app;

        $this->userResolver = $userResolver;
    }

   /**
    * Determine if a given ability has been defined.
    *
    * @param string $ability
    * @return bool
    */
    public function has($ability)
    {
        $abilities = is_array($ability) ? $ability : func_get_args();

        foreach ($abilities as $ability)
        {
            if (!isset($this->abilities[$ability]))
            {
                return false;
            }
        }

        return true;
    }


   /**
    * Define a new ability.
    *
    * @param string  $ability
    * @param string|callable  $callback
    * @return $this
    */
    public function define($ability, $callback)
    {
        if (is_callable($callback))
        {
            $this->abilities[$ability] = $callback;
        }
        else
        {
            throw new InvalidArgumentException(
                "Callback must be a callable"
            );
        }

        return $this;
    }

   /**
    * Define a policy class for a given class type.
    *
    * @param  string  $class
    * @param  string|null  $policy
    * @return $this
    */
    public function policy($class, $policy = null)
    {
        if (is_array($class))
        {
            foreach ($class as $key => $value)
            {
                $this->policy($key, $value);
            }

            return $this;
        }

        $this->policies[$class] = $policy;

        return $this;
    }

    /**
    * Get all of the defined abilities.
    *
    * @return array
    */
    public function abilities(array $abilities = [])
    {
        return $this->abilities = $abilities ?? $this->abilities;
    }

   /**
    * Determine if the given ability should be granted for the current user.
    *
    * @param  string  $ability
    * @param  array|mixed  $arguments
    * @return bool
    */
    public function allows($ability, $arguments = [])
    { 
        return $this->check($ability, $arguments);
    }

   /**
    * Determine if the given ability should be denied for the current user.
    *
    * @param  string  $ability
    * @param  array|mixed  $arguments
    * @return bool
    */
    public function denies($ability, $arguments = [])
    {
        return ! $this->allows($ability, $arguments);
    }

   /**
    * Determine if all of the given abilities should be granted for the current user.
    *
    * @param  iterable|string  $abilities
    * @param  array|mixed  $arguments
    * @return bool
    */
    public function check($abilities, $arguments = [])
    {
        $abilities = is_array($abilities) ? $abilities : [$abilities];

        foreach ($abilities as $ability)
        {
            if (!$this->inspect($ability, $arguments)->allowed())
            {
                return false;
            }
        }

        return true;
    }

   /**
    * Determine if the given ability should be granted for the current user.
    *
    * @param  string  $ability
    * @param  array|mixed  $arguments
    * @return object Xcholars\Auth\Access\Response
    *
    * @throws object Xcholars\Auth\Access\AuthorizationException
    */
    public function authorize($ability, $arguments = [])
    {
        return $this->inspect($ability, $arguments)->authorize();
    }

   /**
    * Inspect the user for the given ability.
    *
    * @param  string  $ability
    * @param  array|mixed  $arguments
    * @return object Xcholars\Auth\Access\Response
    */
    public function inspect($ability, $arguments = [])
    {
        try
        {
            $result = $this->raw($ability, $arguments);

        if ($result instanceof Response)
        {
            return $result;
        }

        return $result ? Response::allow() : Response::deny();

        }
        catch (AuthorizationException $error)
        {
            return $error->toResponse();
        }
    }

   /**
    * Get the raw result from the authorization callback.
    *
    * @param  string  $ability
    * @param  array|mixed  $arguments
    * @return mixed
    *
    * @throws object Xcholars\Auth\Access\AuthorizationException
    */
    public function raw($ability, $arguments = [])
    {
        $arguments = is_array($arguments) ? $arguments : [$arguments];

        $user = $this->resolveUser();

        return $this->callAuthCallback($user, $ability, $arguments);
    }

   /**
    * Get a guard instance for the given user.
    *
    * @param object  Xcholars\Database\Orm\Model
    * @return static
    */
    public function forUser($user)
    {
        $callback = function () use ($user)
        {
           return $user;
        };

        $static = new static($this->app, $callback);

        $static->policy($this->policies);

        $static->abilities($this->abilities);

        return $static;
    }

   /**
    * Resolve the user from the user resolver.
    *
    * @return mixed
    */
    private function resolveUser()
    {
        return call_user_func($this->userResolver);
    }

   /**
    * Resolve and call the appropriate authorization callback.
    *
    * @param object Xcholars\Database\Orm\Model $user
    * @param  string  $ability
    * @param  array  $arguments
    * @return bool
    */
    private function callAuthCallback($user, $ability, array $arguments)
    {
        $callback = $this->resolveAuthCallback($user, $ability, $arguments);

        return $callback($user, ...$arguments);
    }

   /**
    * Resolve the callable for the given ability and arguments.
    *
    * @param object Xcholars\Database\Orm\Model $user
    * @param  string  $ability
    * @param  array  $arguments
    * @return callable
    */
    private function resolveAuthCallback($user, $ability, array $arguments)
    {
        if (count($arguments))
        {
            [$class] = $arguments;

            $policy = $this->getPolicyFor($class);

            $callback = $this->resolvePolicyCallback(
                            $user, $ability, $arguments, $policy
                        );

            if (!is_null($policy) && $callback)
            {
               return $callback;
            }
        }

        if (isset($this->abilities[$ability]))
        {
            return $this->abilities[$ability];
        }

        return function ()
        {
            return null;
        };
    }

   /**
    * Get a policy instance for a given class.
    *
    * @param  object|string  $class
    * @return mixed
    */
    public function getPolicyFor($class)
    {
        if (is_object($class))
        {
            $class = get_class($class);
        }


        if (! is_string($class))
        {
           return;
        }

        if (isset($this->policies[$class]))
        {
            return $this->resolvePolicy($this->policies[$class]);
        }

        return null;
    }

    /**
    * Build a policy class instance of the given type.
    *
    * @param  object|string  $class
    * @return mixed
    */
    public function resolvePolicy($class)
    {
        return $this->app->make($class);
    }

   /**
    * Resolve the callback for a policy check.
    *
    * @param object  Xcholars\Database\Orm\Model $user
    * @param  string  $ability
    * @param  array  $arguments
    * @param  mixed  $policy
    * @return bool|callable
    */
    private function resolvePolicyCallback($user, $ability, array $arguments, $policy)
    {
        if (! is_callable([$policy, $ability]))
        {
            return false;
        }

        return function () use ($user, $ability, $arguments, $policy)
        {
            return $this->callPolicyMethod($policy, $ability, $user, $arguments);
        };

    }

   /**
    * Call the appropriate method on the given policy.
    *
    * @param  mixed  $policy
    * @param  string  $method
    * @param object  Xcholars\Database\Orm\Model $user
    * @param  array  $arguments
    * @return mixed
    */
    private function callPolicyMethod($policy, $method, $user, array $arguments)
    {
        if (isset($arguments[0]) && is_string($arguments[0]))
        {
            array_shift($arguments);
        }

        if (! is_callable([$policy, $method]))
        {
            return;
        }

        if (!is_null($user))
        {
            return $policy->{$method}($user, ...$arguments);
        }
    }


}
