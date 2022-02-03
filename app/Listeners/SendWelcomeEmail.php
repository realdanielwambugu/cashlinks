<?php

namespace App\Listeners;

use App\Events\UserRegitered;

use Xcholars\Mail\Mailer;

use Xcholars\Support\Proxies\Settings;

class SendWelcomeEmail
{
   /**
    * Instance of \Xcholars\Mail\Mailer
    *
    * @var object \Xcholars\Mail\Mailer
    */
    private $mail;

   /**
    * Create the event listener.
    *
    * @return void
    */
    public function __construct(Mailer $mail)
    {
        $this->mail = $mail;
    }

   /**
    * Handle the event.
    *
    * @param object App\Events\OrderShipped $event
    * @return void
    */
    public function handle(UserRegitered $event)
    {
        $this->mail->send('WelcomeEmail', $event->user->getAttributes(), function ($mail) use($event)
                {
                    $mail->from(Settings::get('mailers.smtp.mail_from'), 'Welcome Email');

                    $mail->to($event->user->email, $event->user->fullName);

                    $mail->subject('Weilcome to' . app_name());
                });
    }

}
