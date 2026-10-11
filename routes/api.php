<?php
// JSON endpoints.

$router->post('/api/v1/get-flowers',    'Api\FlowersController@getFlowers');
$router->get('/api/v1/flowers/{slug}',  'Api\FlowersController@show');
$router->get('/api/v1/regions',         'Api\FlowersController@regions');
