<?php

use frontend\shared\PostRequest;

/**
 * Proxy → `/src/encargossacd/horario_update_data` (EncargoHorarioUpdate).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
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

PostRequest::getDataFromUrl('/src/encargossacd/horario_update_data', $campos);
header('Content-Type: text/plain; charset=UTF-8');
echo '';
