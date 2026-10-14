<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * Paginación con estilos de Bootstrap 5. Conserva los filtros de la URL.
 *
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>
<nav aria-label="Paginación">
    <ul class="pagination pagination-sm mb-0">
        <li class="page-item <?= $pager->hasPreviousPage() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasPreviousPage() ? $pager->getFirst() : '#' ?>" aria-label="Primera">
                <i class="bi bi-chevron-double-left"></i>
            </a>
        </li>
        <li class="page-item <?= $pager->hasPreviousPage() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasPreviousPage() ? $pager->getPreviousPage() : '#' ?>" aria-label="Anterior">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
            </li>
        <?php endforeach ?>

        <li class="page-item <?= $pager->hasNextPage() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasNextPage() ? $pager->getNextPage() : '#' ?>" aria-label="Siguiente">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
        <li class="page-item <?= $pager->hasNextPage() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasNextPage() ? $pager->getLast() : '#' ?>" aria-label="Última">
                <i class="bi bi-chevron-double-right"></i>
            </a>
        </li>
    </ul>
</nav>
