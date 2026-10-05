<?php
$session = session();
$rol = $session->get('rol');
$activeTab = $activeTab ?? 'todos';
?>
<div class="ticket-tabs">

    <?php if ($rol === 'Administrador'): ?>
    <a href="<?= base_url('/dashboard') ?>" class="<?= $activeTab === 'todos' ? 'active' : '' ?>">
        Todos
    </a>
    <?php endif; ?>

    <?php if ($rol === 'Administrador'): ?>
    <a href="<?= base_url('/tickets/asignados') ?>" class="<?= $activeTab === 'asignados' ? 'active' : '' ?>">
        Asignados
    </a>
    <?php endif; ?>

    <?php if ($rol === 'Administrador'): ?>
    <a href="<?= base_url('/tickets/cola') ?>" class="<?= $activeTab === 'cola' ? 'active' : '' ?>">
        Sin asignar
    </a>
    <?php endif; ?>

    <!-- Todos los roles pueden ver "Mis tickets", o quizás para Solicitante sea la única pestaña -->
    <a href="<?= base_url('/tickets/mis-tickets') ?>" class="<?= $activeTab === 'mis_tickets' ? 'active' : '' ?>">
        Mis tickets
    </a>

</div>
