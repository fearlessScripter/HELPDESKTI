<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Auth::index'); // La ruta principal lleva al login
$routes->get('/auth', 'Auth::index');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Dashboard::index');

// Rutas de navegación pura (Vistas)
$routes->get('/tickets/nuevo', 'Tickets::nuevo');// Crearemos este controlador después
// Ruta para la Cola de Tickets del Administrador
$routes->get('/tickets/cola', 'Tickets::cola');
// Rutas de Tickets (Solicitante y Técnico)
$routes->get('/tickets/mis-tickets', 'Tickets::misTickets');
$routes->get('/tickets/asignados', 'Tickets::asignados');

// Rutas de Administrador y Base de Conocimientos
$routes->get('/reportes', 'Reportes::index');
$routes->get('/usuarios', 'Usuarios::index');
$routes->get('/base-conocimientos', 'BaseConocimientos::index');