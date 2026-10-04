<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Auth::index'); // La ruta principal lleva al login
$routes->get('/auth', 'Auth::index');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Dashboard::index'); // Crearemos este controlador después