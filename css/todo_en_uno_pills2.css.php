<?php
/**
 * Capa de contenido para layout `pills2` (misma base que pills, selector body.layout-pills2).
 */
ob_start();
include __DIR__ . '/todo_en_uno_pills.css.php';
$css = ob_get_clean();
$css = str_replace('body.layout-pills', 'body.layout-pills2', $css);
$css = str_replace(
    ':not(.pills-pill):not(.pills-user-trigger)',
    ':not(.pills-pill):not(.pills-user-trigger):not(.pills2-workspace-trigger):not(.pills2-workspace-option):not(.pills2-user-trigger)',
    $css
);
echo $css;
