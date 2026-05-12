<?php
/**
 * Sustituye a {@see slickgrid_orbix.css.php} con el layout `modern`: conserva reglas legacy
 * y ajusta cabeceras al mismo criterio que las tablas HTML (sin bloque marino).
 */
include __DIR__ . '/slickgrid_orbix.css.php';
?>
<style>
body.layout-modern .selected {
    background-color: <?= $tono3 ?> !important;
}

body.layout-modern .slick-row.selected .cell-selection {
    background-color: <?= $tono4 ?> !important;
    color: <?= $oscuro ?>;
}

body.layout-modern .active-row {
    background-color: <?= $tono2 ?> !important;
}

body.layout-modern .cell-selection {
    background: #e8eaef;
    color: <?= $letras ?>;
    border-right-color: <?= $lineas ?>;
}

body.layout-modern .slick-header.ui-state-default {
    border-top-color: <?= $lineas ?>;
    background: linear-gradient(180deg, <?= $tono4 ?> 0%, <?= $tono5 ?> 100%);
}

body.layout-modern .slick-header-column.ui-state-default {
    border-right-color: <?= $lineas ?>;
    font-weight: 600;
    color: <?= $oscuro ?>;
}

body.layout-modern .slick-cell {
    border-bottom-color: <?= $lineas ?>;
}
</style>
