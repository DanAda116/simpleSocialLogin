<?php
/**
 * Created by PhpStorm.
 * User: Danielczyk
 * Date: 15.11.2018
 * Time: 13:27
 */

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadService
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {

        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDir(), $fileName);
        } catch (FileException $e) {
            echo $e->getMessage();
        }

        return $fileName;
    }

    public function getTargetDir()
    {
        return $this->targetDirectory;
    }

    public static function saveAvatarImage($url)
    {
        $content = file_get_contents($url);


        $fileName = md5(uniqid()).'.jpg';
        $file = 'uploads/avatars/'.$fileName;
        $fp = fopen($file, "w");
        fwrite($fp, $content);
        fclose($fp);

        return $fileName;
    }

}