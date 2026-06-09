<?php

use frontend\shared\FrontBootstrap;

/**
 * Compatibilidad: despacha por `que` a los controladores finos (mismo patrón que ubis_lista.php).
 */

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'nuevo':
        require __DIR__ . '/calendario_periodos_nuevo.php';
        break;
    case 'form_periodo':
        require __DIR__ . '/calendario_periodos_form_periodo.php';
        break;
    case 'get2':
        require __DIR__ . '/calendario_periodos_get2.php';
        break;
    case 'get':
        require __DIR__ . '/calendario_periodos_get.php';
        break;
}
