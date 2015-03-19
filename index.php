<?php

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/vendor/autoload.php';

//define('PROCESSED_IMAGES', 'processed_images');

define('PROCESSED_IMAGES', 'processed_images');

define('LAST_IMAGE', 'last_image');

define('LAST_IMAGE_NAME', 'last_image_name');

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->post('/trace', function(Request $request) use($app) {
    return (new \Brainly\Action\Trace($request, new \Brainly\ImageProcess()))->getRecognizedText();
});

$app->get('/last', function() use ($app) {
    $filePath = file_get_contents(LAST_IMAGE_NAME);
    return $app['twig']->render('last.twig', array(
        'filepath' => $filePath
    ));
});


$app->run();
