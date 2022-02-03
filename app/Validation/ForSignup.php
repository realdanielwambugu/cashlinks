<?php

Namespace App\Validation;

class ForSignup
{
   /**
    * Validation rules defination.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'username' => 'unique:users|required|min:3|max:20',
            'email' => 'email|unique:users|required',
            'password' => 'required|min:6',
        ];
    }

   /**
    * Error messages mappings.
    *
    * @param string|null $rule
    * @return array
    */
    public function messages($rule = null)
    {
        $messages = [

        ];

        return  $messages[$rule] ?? $messages;
    }



}
