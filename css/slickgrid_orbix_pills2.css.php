<?php
/**
 * SlickGrid para layout `pills2` (misma base que pills, selector body.layout-pills2).
 */
ob_start();
include __DIR__ . '/slickgrid_orbix_pills.css.php';
echo str_replace('body.layout-pills', 'body.layout-pills2', ob_get_clean());
