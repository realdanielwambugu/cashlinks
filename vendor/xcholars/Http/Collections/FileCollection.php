<?php

Namespace Xcholars\Http\Collections;

use Xcholars\Storage\File;

use Xcholars\Storage\FileSystem;

class FileCollection extends InputCollection
{
   /**
    * File system instance
    *
    * @var object Xcholars\Storage\FileSystem
    */
    private $storage;

    /**
    * Create new FileCollection instance
    *
    * @return void
    */
    public function __construct(array $parameters)
    {
        $this->storage = new FileSystem(upload_path());

        $this->add($parameters);
    }

    /**
    * get a parameter by name.
    *
    * @param string $key
    * @param array $file
    * @return void
    */
    public function setFile($key, $file)
    {
        if (!is_array($file))
        {
            throw new \InvalidArgumentException('An uploaded file must be an
            array or an instance of FileMetaData.');
        }

        $file = $this->convertToFileMetaDataInstance($key);

        $this->set($key, $file);

    }

    /**
    * Convert file array to FileMetaData instance
    *
    * @param string $key
    * @return object Xcholars\Storage\FileMetaData
    */
    private function convertToFileMetaDataInstance($key)
    {
        return new File($key, $this->storage);
    }

    /**
    * add files to the collection
    *
    * @param array $files
    * @return void
    */
    public function add(array $files)
    {
        foreach ($files as $key => $file)
        {
            $this->setFile($key, $file);
        }
    }

    public function uploadTo($directory = null)
    {
        foreach ($this->all() as $file)
        {
            $file->setDirectory($directory);

            $file->setName(md5(uniqid()));

            try
            {
                 $file->upload();
            }
            catch (\Exception $e)
            {
               throw new \Exception(
                   "File [{$file->getNameWithExtension()}] Not uploaded"
               );
            }
        }

        return $this;
    }

}
