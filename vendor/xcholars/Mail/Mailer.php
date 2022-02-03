<?php

Namespace Xcholars\Mail;

use Xcholars\Exceptions\NotFoundException;

use RuntimeException;

class Mailer
{
   /**
    * Base Mailer Instance
    *
    * @var object Xcholars\Mail\MailContract
    */
    private $mailer;

   /**
    * create new instance of Mailer
    *
    * @param object Xcholars\Mail\MailContract
    * @return void
    */
    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

   /**
    * send the email with the given data
    *
    * @param string $template
    * @param array $data
    * @param callback $callback
    * @return void
    */
    public function send($template, $data, $callback)
    {
        $message = new Message($this->mailer);

        $template = $this->prepareTemplate($template, $data);

        $message->body($template);

        call_user_func($callback, $message);

        if(!$this->mailer->send())
        {
           throw new RuntimeException($this->mailer->ErrorInfo);
        }
    }

   /**
    * pass data to the template file
    *
    * @param string $template
    * @param array $data
    * @return mixed $template
    */
    public function prepareTemplate($template, $data)
    {
       $template = $this->getTemplate($template);

        extract($data);

        ob_start();

        require_once $template;

        $template = ob_get_clean();

        ob_end_clean();

        return $template;
    }

   /**
    * check if template file is defined
    *
    * @param string $template
    * @return bool
    */
    public function hasTemplate($template)
    {
        return file_exists($template);
    }

   /**
    *get the template file path
    *
    * @param string $basename
    * @return mixed $template
    */
    public function getTemplate($basename)
    {
        $template = view('mail' . DIRECTORY_SEPARATOR . $basename);

        if (!$this->hasTemplate($template))
        {
          throw new NotFoundException("Email template {$template} not found");

        }

        return $template;
    }


}
