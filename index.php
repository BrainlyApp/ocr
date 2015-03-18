<?php

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/vendor/autoload.php';

define('IMAGES_PATH', 'images');

$app = new Silex\Application();

$app->post('/trace', function(Request $request) use($app) {
    $file = $request->files->get('file');

    $filePath = sprintf('%s/%s', $file->getPath(), $file->getFileName());

    $tesseract = new TesseractOCR($filePath);
    $tesseract->setLanguage('pol');
    $content = $tesseract->recognize();


    return json_encode([
        'success' => true,
        'content' => $content
    ]);
});

$app->get('/last');

$app->run();
