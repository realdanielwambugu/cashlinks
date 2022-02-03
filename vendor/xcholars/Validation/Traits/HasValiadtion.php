<?php

Namespace Xcholars\Validation\Traits;

use Xcholars\Http\Request;

use Xcholars\Validation\Factory;

trait HasValiadtion
{
    private $validator;

    public function setValidator()
    {
        $this->validator = Factory::makeValidator();
    }

    public function validate(Request $request)
    {
        $this->setValidator();

        $this->validator->check($request->all());

        return $this->validator;
    }

}
