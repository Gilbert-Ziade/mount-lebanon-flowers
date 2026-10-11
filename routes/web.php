<?php
// Public pages (return HTML). $router is created in public/index.php.

$router->get('/',                'PageController@home');
$router->get('/flowers',         'FlowerPageController@index');   // all flowers + filters
$router->get('/flowers/{slug}',  'FlowerPageController@show');    // one flower
$router->get('/about',           'PageController@about');
$router->get('/missing',         'MissingFlowerController@show'); // "Can't find a flower?"
$router->post('/missing/whatsapp', 'MissingFlowerController@send'); // redirects to WhatsApp
