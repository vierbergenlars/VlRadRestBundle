<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface;

require __DIR__.'/../../../vendor/autoload.php';

/**
 * A simple file resource, it represents one file in a specific directory.
 */
class File
{
    private $data;
    private $filename;

    /**
     * A getId() function is required when using the packaged RadRestController
     * @codeCoverageIgnore
     */
    public function getId()
    {
        return $this->getFilename();
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}

/**
 * Manages all files in a given directory
 */
class FileResourceManager implements ResourceManagerInterface
{
    private $basedir;

    public function __construct($basedir)
    {
        $this->basedir = $basedir;
    }

    /**
     * Locates all files in the directory, and returns them as an array of File objects
     * @return array
     */
    public function findAll()
    {
        $files_in_directory = scandir($this->basedir);
        $files = array();
        foreach($files_in_directory as $filename)
        {
            if(is_file($this->basedir.'/'.$filename))
                $files[] = $this->find($filename);
        }

        return $files;
    }

    /**
     * Tries to find a file by its identifier (filename) and return its object representation.
     * If no such file exists, null is returned instead
     * @param string $filename
     * @return File|null
     */
    public function find($filename)
    {
        if(!is_file($this->basedir.'/'.$filename))
            return null;

        $file = new File();
        $file->setFilename($filename);
        $file->setData(file_get_contents($this->basedir.'/'.$filename));

        return $file;
    }

    /**
     * Creates a new, empty File object, which has no data or only default data
     * @return File
     */
    public function create()
    {
        return new File();
    }

    /**
     * Updates a file with a File object. A logic exception will be thrown if the passed object is not of the expected type.
     * @param File $file
     */
    public function update($file)
    {
        if(!$file instanceof File) {
            throw new \LogicException('FileResourceManager::update() expected an instance of File, but got an instance of '.get_class($file));
        }
        file_put_contents($this->basedir.'/'.$file->getFilename(), $file->getData());
    }

    /**
     * Deletes a file referenced by a File object. A logic exception will be thrown if the passed object is not of the expected type.
     * @param File $file
     */
    public function delete($file)
    {
        if(!$file instanceof File) {
            throw new \LogicException('FileResourceManager::delete() expected an instance of File, but got an instance of '.get_class($file));
        }
        unlink($this->basedir.'/'.$file->getFilename());
    }
}


// Execute the following sample only when the file is run directly
if(realpath($_SERVER['SCRIPT_FILENAME']) == realpath(__FILE__)) {
    $fileManager = new FileResourceManager(__DIR__);
    var_dump($fileManager->findAll());

    $scratchFile = $fileManager->create();  echo 'Note, no file has been created yet on the filesystem';
    fread(STDIN, 1); // Wait for keypress

    $scratchFile->setFilename('scratch.tmp');
    $scratchFile->setData('123456789');
    $fileManager->update($scratchFile); echo 'Now, the file has been created';
    fread(STDIN, 1);

    $scratchFile->setData('987654321'); echo 'Not automatically updated';
    fread(STDIN, 1);

    $fileManager->update($scratchFile); echo 'But only after calling update()';
    fread(STDIN, 1);

    $fileManager->delete($scratchFile); echo 'Finally, remove the scratch file';
}
