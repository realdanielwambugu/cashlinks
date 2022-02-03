<?php

Namespace Xcholars\Validation;

use Xcholars\ApplicationContract;

class Factory
{
   /**
    * service Container instance
    *
    * @var object Xcholars\ApplicationContract
    */
    private static $app;

   /**
    * set the application instance
    *
    * @param object Xcholars\Routing\Group\GroupStack $stack
    * @param object  Xcholars\ApplicationContract $app
    * @return void
    */
    public static function setApp(ApplicationContract $app)
    {
        static::$app = $app;
    }

   /**
    * Create new instance of request
    *
    * @return object Xcholars\Http\Request
    */
    public static function makeValidator()
    {
        return static::$app->make(Validator::class);
    }

}
