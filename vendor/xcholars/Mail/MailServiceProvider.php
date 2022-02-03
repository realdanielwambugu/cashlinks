<?php

Namespace Xcholars\Mail;

use Xcholars\Provider\ServiceProvider;

use \Xcholars\Settings\SettingsContract as Settings;

use \Xcholars\Mail\MailContract;

class MailServiceProvider extends ServiceProvider
{
  /**
   * register bindings with the service container.
   *
   * @return object
   */
   public function register()
   {
        $this->app->singleton(MailContract::class, function ($value='')
        {
            return new \PHPMailer\PHPMailer\PHPMailer;
        });

        $this->app->bind(\Xcholars\Mail\Mailer::class, function ($app)
        {
            return new \Xcholars\Mail\Mailer($app->make(MailContract::class));
        });

   }

  /**
   * Activities to be performed after bindings are registerd.
   *
   * @return void
   */
   public function boot()
   {
        $settings = $this->app->make(Settings::class);

        $mailer = $settings->get('mail.default');

        $config = $settings->get("mail.mailers.{$mailer}");

        $mail = $this->app->make(MailContract::class);

        $mail->isSMTP($config['transport'] === 'smtp');

        $mail->SMTPDebug  = $config['smtp_debug'];

        $mail->Host = $config['host'];

        $mail->Port = $config['port'];

        $mail->SMTPSecure = $config['smtp_secure'];

        $mail->SMTPAuth = $config['smtp_auth'];

        $mail->Username = $config['username'];

        $mail->Password = $config['password'];

        $mail->isHTML($config['html']);
   }
}
