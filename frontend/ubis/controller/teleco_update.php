<?php

require_once("frontend/shared/global_header_front.inc");

$Qmod = (string)filter_input(INPUT_POST, 'mod');
if ($Qmod === 'eliminar_teleco') {
    require __DIR__ . '/teleco_eliminar_ajax.php';
    return;
}
require __DIR__ . '/teleco_guardar_ajax.php';
