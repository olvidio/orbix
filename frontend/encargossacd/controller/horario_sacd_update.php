<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\FrontBootstrap;

/**
 * Proxy → `/src/encargossacd/horario_sacd_update_data` (EncargoSacdHorarioUpdate).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$keys = [
    'filtro_sacd',
    'id_nom',
    'id_enc',
    'id_item',
    'desc_enc',
    'mod',
    'f_ini',
    'f_fin',
    'dia',
    'dia_ref',
    'dia_num',
    'mas_menos',
    'dia_inc',
    'h_ini',
    'h_fin',
];
$campos = [];
foreach ($keys as $k) {
    if (isset($_POST[$k])) {
        $campos[$k] = $_POST[$k];
    }
}

AjaxJsonSupport::proxyPostRequest('/src/encargossacd/horario_sacd_update_data', $campos);
