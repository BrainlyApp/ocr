<?php

namespace Brainly\Action;

use Brainly\Image;
use Brainly\ImageProcess;
use Symfony\Component\HttpFoundation\Request;

class Trace
{


    /**
     * @var Request
     */
    private $request;

    /**
     * @var ImageProcess
     */
    private $imageProcess;

    /**
     * @var \TesseractOCR
     */
    private $tesseract;

    private $scannedText;

    public function __construct(Request $request, ImageProcess $imageProcess)
    {
        $this->request = $request;
        $this->imageProcess = $imageProcess;
    }

    public function getRecognizedText()
    {
        $processedFile = $this->request->files->get('file');

        $image = new \Imagick($this->getRecivedFilePath($processedFile));

        $image = $this->imageProcess->convertToGrayScale($image);
        $avg = $this->imageProcess->getAverageColor($image);
        $image = $this->imageProcess->pixelsBelowToBlack($image, $avg);
        $image = $this->imageProcess->convertToTiff($image);

        $image = $this->imageProcess->setImageFilename(PROCESSED_IMAGES, $image);
        $this->saveImageInDestination($image);
        $this->initializeTesseract($image);
        $this->scannedText = $this->scanText();
        $this->removeImageFromDisk($image);
        $this->processLastImage($image);

        return json_encode([
            'success' => true,
            'content' => $this->scannedText
        ]);
    }

    private function getRecivedFilePath($file)
    {
        return sprintf('%s/%s', $file->getPath(), $file->getFileName());
    }

    private function saveImageInDestination(\Imagick $image)
    {
        $image->writeImage();
    }

    private function initializeTesseract(\Imagick $image)
    {
        $this->tesseract = new \TesseractOCR($image->getImageFilename());
    }

    private function scanText()
    {
        $this->tesseract->setLanguage('pol');
        return $this->tesseract->recognize();
    }

    private function removeImageFromDisk(\Imagick $image)
    {
        unlink($image->getImageFilename());
    }

    private function processLastImage(\Imagick $image)
    {
        $image = $this->imageProcess->convertToJpg($image);
        $image = $this->imageProcess->setImageFilename(LAST_IMAGE, $image);
        $lastImageName = file_get_contents(LAST_IMAGE_NAME);
        if (!empty($lastImageName)) {
            unlink($lastImageName);
        }
        $this->saveImageInDestination($image);
        file_put_contents(LAST_IMAGE_NAME, $image->getImageFilename());
    }

}
