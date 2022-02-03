<?php

namespace App\Listeners;

use App\Events\EmailExist;

use Xcholars\Mail\Mailer;

use Xcholars\Support\Proxies\Settings;

class SendResetCodeEmail
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
    public function handle(EmailExist $event)
    {
        $this->mail->send('ResetCodeEmail', $event->user->getAttributes(), function ($mail) use($event)
                {
                    $mail->from(Settings::get('mail.mailers.smtp.mail_from'), 'Reset Code');

                    $mail->to($event->user->email, $event->user->fullName);

                    $mail->subject('Forgot Password Code');
                });
    }

}
