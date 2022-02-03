<?php

Namespace Xcholars;

use Xcholars\Container\Container;

use Xcholars\Settings\SettingsContract;

use Xcholars\Provider\ProvidersRegistrar;

use Xcholars\Provider\ProvidersBootstrapper;

use Xcholars\Exceptions\ExceptionaHandler;

use Xcholars\Env\EnvironmentVariables;

class Application extends Container implements ApplicationContract
{
   /**
    * Application name
    *
    * @var string
    */
    private $name;

   /**
    * Application base path
    *
    * @var string
    */
    private $basePath;

   /**
    * Application instance.
    *
    * @var object
    */
    private static $instance;

   /**
    * boot application core services
    *
    * @return void
    */
    public function boot()
    {
        static::setInstance($this);

        $this->loadSettings();

        $this->setbasePath();

        $this->setName();

        $this->registerProviders();

        $this->bootProviders();
    }

   /**
    * set static instance of the application .
    *
    * @param object Xcholars\ApplicationContract
    * @return void
    */
    private static function setInstance(ApplicationContract $app)
    {
        static::$instance = $app;
    }

   /**
    * get container instance.
    *
    * @return object
    */
    public static function getInstance()
    {
        if (is_null(static::$instance))
        {
           static::$instance = new static;
        }

        return static::$instance;
    }

   /**
    * Register and boot Exception handler for the application
    *
    * @return object
    */
    public function bootExceptionHandler()
    {
         return $this->make(ExceptionaHandler::class)->boot();
    }

   /**
    * Load app environment variables using vlucas a dependency for loading env files
    *
    * @param string $path
    * @return object
    */
    public function loadEnvironmentVariables($path)
    {
        return $this->make(EnvironmentVariables::class)->load($path);
    }

   /**
    * set the Application base url
    *
    * @param string $basePath
    * @return void
    */
    public function setbasePath($basePath = null)
    {
        $this->basePath = $basePath ?? $this->loadSettings()->get('app.base_path');
    }

   /**
    * set the Application name
    *
    * @param string $name
    * @return void
    */
    public function setName($name = null)
    {
        $this->name = $name ?? $this->loadSettings()->get('app.app_name');
    }

   /**
    * get the Application base url
    *
    * @return string
    */
    public function getBasePath()
    {
        return $this->basePath;
    }

   /**
    * get the Application name
    *
    * @return string
    */
    public function getName()
    {
        return $this->name;
    }


   /**
    * load application settings from config files
    *
    * @return array
    */
    public function loadSettings()
    {
        return $this->make(SettingsContract::class);
    }

   /**
    * register all providers with the service Container.
    *
    * @return void
    */
    private function registerProviders()
    {
        $registrar = $this->make(ProvidersRegistrar::class)->register();
    }

   /**
    * boot all registered providers
    *
    * @param array $providers
    * @return void
    */
    private function bootProviders()
    {
        $this->make(ProvidersBootstrapper::class)->boot();
    }

}
