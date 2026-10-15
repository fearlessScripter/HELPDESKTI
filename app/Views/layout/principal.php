<?php
/**
 * Layout común de las páginas internas (todo menos el login).
 *
 * Cada página hace:
 *   <?= $this->extend('layout/principal') ?>
 *   <?php $this->setVar('menuActivo', 'tickets'); ?>                  // ítem resaltado del menú lateral
 *   <?php $this->setVar('topbar', ['pageTitle' => 'Mis Tickets']); ?>  // datos de la barra superior
 * y llena las secciones: titulo, estilos (opcional), contenido, modales (opcional), scripts (opcional).
 */
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="<?= tema_bootstrap() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('titulo') ?></title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Estilos comunes del sistema -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    <?= $this->renderSection('estilos') ?>
    <?= view('layout/tema') ?>
</head>
<body>

<?= view('layout/sidebar', ['active' => $menuActivo ?? 'inicio']) ?>

<div class="main-content">
    <?= view('layout/topbar', $topbar ?? []) ?>
<?= $this->renderSection('contenido') ?>
</div>

<?= $this->renderSection('modales') ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
