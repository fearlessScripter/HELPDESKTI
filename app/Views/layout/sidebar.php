<?php
$session = session();
$rol = $session->get('rol');
$active = $active ?? 'inicio';
?>
<div class="sidebar">

    <div class="sidebar-title">
        <i class="bi bi-headset"></i>
        HelpDesk TI
    </div>

    <!-- Inicio: Visible para todos -->
    <a href="<?= base_url('/dashboard') ?>" class="<?= $active === 'inicio' ? 'active' : '' ?>">
        <i class="bi bi-house-door"></i>
        <span>Inicio</span>
    </a>

    <!-- Tickets: Visible para todos (Solicitante, Técnico, Admin) -->
    <a href="<?= base_url('/tickets/mis-tickets') ?>" class="<?= $active === 'tickets' ? 'active' : '' ?>">
        <i class="bi bi-ticket-detailed"></i>
        <span>Tickets</span>
    </a>

    <!-- Usuarios: Solo Administrador -->
    <?php if ($rol === 'Administrador'): ?>
    <a href="<?= base_url('/usuarios') ?>" class="<?= $active === 'usuarios' ? 'active' : '' ?>">
        <i class="bi bi-people"></i>
        <span>Usuarios</span>
    </a>
    <?php endif; ?>

    <!-- Base de conocimientos: Técnico y Administrador -->
    <?php if ($rol === 'Administrador' || $rol === 'Técnico'): ?>
    <a href="<?= base_url('/base-conocimientos') ?>" class="<?= $active === 'base_conocimientos' ? 'active' : '' ?>">
        <i class="bi bi-book"></i>
        <span>Base de conocimientos</span>
    </a>
    <?php endif; ?>

    <!-- Reportes: Solo Administrador -->
    <?php if ($rol === 'Administrador'): ?>
    <a href="<?= base_url('/reportes') ?>" class="<?= $active === 'reportes' ? 'active' : '' ?>">
        <i class="bi bi-bar-chart"></i>
        <span>Reportes</span>
    </a>
    <?php endif; ?>

</div>
