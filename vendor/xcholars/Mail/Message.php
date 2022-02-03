<?php

Namespace Xcholars\Mail;

class Message
{
   /**
    * Base Mailer Instance
    *
    * @var object Xcholars\Mail\MailContract
    */
    private $mailer;

   /**
    * create new instance of message
    *
    * @param object Xcholars\Mail\MailContract
    * @return void
    */
    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

   /**
    * set the sender address
    *
    * @param object $address
    * @param object $title
    * @return void
    */
    public function from($address, $title = null)
    {  
        $this->mailer->setFrom($address, $title);
    }

   /**
    * set the recipient address
    *
    * @param object $address
    * @param object $name
    * @return void
    */
    public function to($address, $name = null)
    {
        $this->mailer->addAddress($address, $name);
    }

   /**
    * set the email subject
    *
    * @param object $subject
    * @return void
    */
    public function subject($subject)
    {
        $this->mailer->Subject = $subject;
    }

   /**
    * set the email body content
    *
    * @param object $body
    * @return void
    */
    public function body($body)
    {
        $this->mailer->Body = $body;
    }

}
