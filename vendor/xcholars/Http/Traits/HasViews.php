<?php

Namespace Xcholars\Http\Traits;

use Xcholars\Support\Proxies\Template;

trait HasViews
{
    public function withView($name, array $data = [])
    {

        foreach ($data as $key => $value)
        {
            $$key = $value;
        }

        ob_start();

        $view = view($name);

        if (!file_exists($view))
        {
            $view = view_path($name . '.html');
        }

        require_once $view;

        $template = ob_get_clean();

        ob_end_clean();

        $this->setContent($template);

        return $this;
    }

}
