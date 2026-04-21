<?php

use frontend\shared\PostRequest;

/**
 * Proxy → `/src/encargossacd/horario_sacd_update_data` (EncargoSacdHorarioUpdate).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
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

PostRequest::getDataFromUrl('/src/encargossacd/horario_sacd_update_data', $campos);
header('Content-Type: text/html; charset=UTF-8');
echo '<p class="ok">' . htmlspecialchars(_('Guardado.'), ENT_QUOTES, 'UTF-8') . '</p>';
