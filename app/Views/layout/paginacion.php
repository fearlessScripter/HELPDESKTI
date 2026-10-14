<?php
/**
 * Pie de tabla con resumen, selector de registros por página y paginación.
 *
 * @var \CodeIgniter\Pager\Pager $pager
 * @var string|null              $formId  ID del formulario de filtros (para que el selector "por página" conserve los filtros)
 */
$grupo     = $grupo ?? 'default';
$total     = $pager->getTotal($grupo);
$porPagina = $pager->getPerPage($grupo);
$actual    = $pager->getCurrentPage($grupo);
$desde     = $total > 0 ? (($actual - 1) * $porPagina) + 1 : 0;
$hasta     = min($total, $actual * $porPagina);
$formId    = $formId ?? 'filtrosForm';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 px-3 py-3 border-top bg-white">
    <div class="d-flex align-items-center gap-3 small text-muted">
        <span>Mostrando <strong><?= $desde ?></strong>–<strong><?= $hasta ?></strong> de <strong><?= $total ?></strong> registros</span>
        <div class="d-flex align-items-center gap-1">
            <label for="porPagina" class="text-nowrap mb-0">Por página:</label>
            <select id="porPagina" name="por_pagina" form="<?= esc($formId) ?>" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <?php foreach ([10, 25, 50] as $n): ?>
                    <option value="<?= $n ?>" <?= $porPagina === $n ? 'selected' : '' ?>><?= $n ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php if ($pager->getPageCount($grupo) > 1): ?>
        <?= $pager->links($grupo, 'bootstrap') ?>
    <?php endif; ?>
</div>
