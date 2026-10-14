<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Autenticación
$routes->get('/', 'Auth::index');
$routes->get('/auth', 'Auth::index');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');

// Dashboard
$routes->get('/dashboard', 'Dashboard::index');

// Tickets: Vistas
$routes->get('/tickets/nuevo', 'Tickets::nuevo');
$routes->get('/tickets/cola', 'Tickets::cola');
$routes->get('/tickets/todos', 'Tickets::todos');
$routes->get('/tickets/mis-tickets', 'Tickets::misTickets');
$routes->get('/tickets/asignados', 'Tickets::asignados');
$routes->get('/tickets/detalles/(:segment)', 'Tickets::detalles/$1');
$routes->get('/tickets/detalles', 'Tickets::detalles');

// Tickets: Acciones del Ciclo de Vida
$routes->post('/tickets/crear', 'Tickets::crear');
$routes->post('/tickets/asignar', 'Tickets::asignar');
$routes->post('/tickets/reasignar', 'Tickets::reasignar');
$routes->post('/tickets/resolver', 'Tickets::resolver');
$routes->post('/tickets/cerrar', 'Tickets::cerrar');

// Base de Conocimientos
$routes->get('/base-conocimientos', 'BaseConocimientos::index');
$routes->post('/base-conocimientos/crear', 'BaseConocimientos::crear');
$routes->post('/base-conocimientos/actualizar', 'BaseConocimientos::actualizar');

// Notificaciones
$routes->get('/notificaciones/leer/(:num)', 'Notificaciones::leer/$1');
$routes->post('/notificaciones/leer-todas', 'Notificaciones::leerTodas');

// Usuarios y Reportes
$routes->get('/usuarios', 'Usuarios::index');
$routes->post('/usuarios/crear', 'Usuarios::crear');
$routes->post('/usuarios/actualizar', 'Usuarios::actualizar');
$routes->post('/usuarios/estado', 'Usuarios::cambiarEstado');
$routes->post('/usuarios/password', 'Usuarios::restablecerPassword');
$routes->get('/reportes', 'Reportes::index');