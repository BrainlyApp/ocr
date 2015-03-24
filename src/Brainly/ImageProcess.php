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

    public function getAverageColor(\Imagick $imageOriginal)
    {
        try {
            $image = clone $imageOriginal;
            // Scale down to 1x1 pixel to make Imagick do the average
            $image->scaleimage(1, 1);
            /** @var ImagickPixel $pixel */
            if(!$pixels = $image->getimagehistogram()) {
                return null;
            }
        } catch(ImagickException $e) {
            // Image Magick Error!
            return null;
        } catch(Exception $e) {
            // Unknown Error!
            return null;
        }

        $pixel = reset($pixels);
        $rgb = $pixel->getcolor();

        return sprintf('#%02X%02X%02X', $rgb['r'], $rgb['g'], $rgb['b']);

    }

    public function pixelsBelowToBlack(\Imagick $image, $avg)
    {
        $image->blackThresholdImage($avg);
        return $image;
    }

    public function imageFixOrientation(\Imagick $image) {
        if (method_exists($image, 'getImageProperty')) {
            $orientation = $image->getImageProperty('exif:Orientation');
        } else {
            $filename = $image->getImageFilename();

            if (empty($filename)) {
                $filename = 'data://image/jpeg;base64,' . base64_encode($image->getImageBlob());
            }

            $exif = exif_read_data($filename);
            $orientation = isset($exif['Orientation']) ? $exif['Orientation'] : null;
        }

        if (!empty($orientation)) {
            switch ($orientation) {
                case 3:
                    $image->rotateImage('#000000', 180);
                    break;

                case 6:
                    $image->rotateImage('#000000', 90);
                    break;

                case 8:
                    $image->rotateImage('#000000', -90);
                    break;
            }
        }

        return $image;
    }


}
