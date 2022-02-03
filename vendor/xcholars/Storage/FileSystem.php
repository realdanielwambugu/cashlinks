<?php

Namespace Xcholars\Storage;

class FileSystem extends \Upload\Storage\FileSystem
{
   /**
    * set Directory where the file should be uploaded
    *
    * @param string $directory
    * @return void
    */
    public function setDirectory($directory)
    {
        $this->directory = rtrim($directory, '/') . DIRECTORY_SEPARATOR;
    }

}
