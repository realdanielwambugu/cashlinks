<?php

Namespace Xcholars\Storage;

class File extends \Upload\File
{
   /**
    * set Directory where the file should be uploaded
    *
    * @param string $directory
    * @return void
    */
    public function setDirectory($directory)
    {
        $this->storage->setDirectory($directory);
    }

}
