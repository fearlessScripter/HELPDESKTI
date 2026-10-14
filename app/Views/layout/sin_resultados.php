<?php
/**
 * Fila "sin resultados" de los listados.
 *
 * @var int    $columnas  colspan de la fila
 * @var string $mensaje   Mensaje cuando el listado está vacío sin filtros
 * @var bool   $hayFiltros
 */
?>
<tr>
    <td colspan="<?= (int)$columnas ?>" class="text-center py-4 text-muted">
        <?php if (!empty($hayFiltros)): ?>
            <i class="bi bi-search fs-2 d-block mb-2"></i>
            No se encontraron resultados para la búsqueda o los filtros aplicados.
            <a href="<?= current_url() ?>" class="d-block mt-1 small text-decoration-none">Limpiar búsqueda y filtros</a>
        <?php else: ?>
            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
            <?= esc($mensaje) ?>
        <?php endif; ?>
    </td>
</tr>
