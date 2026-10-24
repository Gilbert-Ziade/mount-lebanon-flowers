<?php
// Public pages (return HTML). $router is created in public/index.php.

use App\Security\JWT\AuthMiddleware;

$router->get('/',                'PageController@home');
$router->get('/flowers',         'FlowerPageController@index');   // all flowers + filters
$router->get('/flowers/{slug}',  'FlowerPageController@show');    // one flower
$router->get('/about',           'PageController@about');
$router->get('/missing',         'MissingFlowerController@show'); // "Can't find a flower?"
$router->post('/missing/whatsapp', 'MissingFlowerController@send'); // redirects to WhatsApp

// Login / forgot password (public, no nav link)
$router->get('/login',            'AuthController@showLogin');
$router->post('/login',           'AuthController@login');
$router->post('/logout',          'AuthController@logout');
$router->get('/forgot-password',  'AuthController@showForgot');
$router->post('/forgot-password', 'AuthController@forgot');
$router->get('/reset-password',   'AuthController@showReset');
$router->post('/reset-password',  'AuthController@reset');

// Admin: every route here needs a valid JWT, otherwise the visitor is sent to /login
$auth = [AuthMiddleware::class];
$router->get('/admin',          'AdminController@index',   $auth);
$router->post('/admin/save',    'AdminController@save',    $auth);
$router->post('/admin/delete',  'AdminController@destroy', $auth);