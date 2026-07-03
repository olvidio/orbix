<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\FrontBootstrap;

/**
 * Proxy → `/src/encargossacd/horario_update_data` (EncargoHorarioUpdate).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$campos = [];
foreach (
    [
        'mod',
        'dia',
        'id_item_h',
        'id_enc',
        'f_ini',
        'f_fin',
        'dia_ref',
        'dia_num',
        'mas_menos',
        'dia_inc',
        'h_ini',
        'h_fin',
        'n_sacd',
        'mes',
    ] as $k
) {
    if (isset($_POST[$k])) {
        $campos[$k] = $_POST[$k];
    }
}
if (isset($_POST['sel_nom'])) {
    $campos['sel_nom'] = $_POST['sel_nom'];
}

AjaxJsonSupport::proxyPostRequest('/src/encargossacd/horario_update_data', $campos);
