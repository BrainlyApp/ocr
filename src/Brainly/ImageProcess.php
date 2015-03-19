<?php

namespace Brainly;

class ImageProcess
{

    public function convertToGrayScale(\Imagick $image)
    {
        $image->setImageColorspace(\Imagick::COLORSPACE_GRAY);
        return $image;
    }

    public function convertToTiff(\Imagick $image)
    {
        $image->setImageFormat('tiff');
        return $image;
    }

    public function setImageFilename($destination, \Imagick $image)
    {
        $filepath = sprintf('%s/%s', $destination, basename($image->getImageFilename()));
        $image->setImageFilename($filepath);
        return $image;
    }

    public function convertToJpg(\Imagick $image)
    {
        $image->setImageFormat('jpg');
        return $image;
    }


}
