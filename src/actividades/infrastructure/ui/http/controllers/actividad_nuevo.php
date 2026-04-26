<?php
/**
 * Endpoint backend AJAX: crea una nueva actividad a partir de los datos del
 * formulario. Responde JSON {success, mensaje?}.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'nuevo').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\actividades\application\ActividadNueva;
use frontend\shared\web\ContestarJson;

$Qinom_tipo_val = (string)filter_input(INPUT_POST, 'inom_tipo_val');
// Puede ser '000' > sin especificar
if (empty($Qinom_tipo_val)) {
    ContestarJson::enviar(_("debe seleccionar un tipo de actividad"));
    exit;
}

$datosActividad = [
    'id_tipo_activ' => (integer)filter_input(INPUT_POST, 'id_tipo_activ'),
    'id_ubi' => (integer)filter_input(INPUT_POST, 'id_ubi'),
    'num_asistentes' => (integer)filter_input(INPUT_POST, 'num_asistentes'),
    'status' => (integer)filter_input(INPUT_POST, 'status'),
    'id_repeticion' => (integer)filter_input(INPUT_POST, 'id_repeticion'),
    'plazas' => (integer)filter_input(INPUT_POST, 'plazas'),
    'tarifa' => (integer)filter_input(INPUT_POST, 'id_tarifa'),
    'precio' => filter_input(INPUT_POST, 'precio', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
    'dl_org' => (string)filter_input(INPUT_POST, 'dl_org'),
    'nom_activ' => (string)filter_input(INPUT_POST, 'nom_activ'),
    'lugar_esp' => (string)filter_input(INPUT_POST, 'lugar_esp'),
    'desc_activ' => (string)filter_input(INPUT_POST, 'desc_activ'),
    'f_ini' => (string)filter_input(INPUT_POST, 'f_ini'),
    'f_fin' => (string)filter_input(INPUT_POST, 'f_fin'),
    'tipo_horario' => (string)filter_input(INPUT_POST, 'tipo_horario'),
    'observ' => (string)filter_input(INPUT_POST, 'observ'),
    'nivel_stgr' => (string)filter_input(INPUT_POST, 'nivel_stgr'),
    'idioma' => (string)filter_input(INPUT_POST, 'idioma'),
    'observ_material' => (string)filter_input(INPUT_POST, 'observ_material'),
    'h_ini' => (string)filter_input(INPUT_POST, 'h_ini'),
    'h_fin' => (string)filter_input(INPUT_POST, 'h_fin'),
    'publicado' => (string)filter_input(INPUT_POST, 'publicado'),
];

$error_txt = '';
try {
    ActividadNueva::actividadNueva($datosActividad);
} catch (Exception $e) {
    $error_txt = $e->getMessage();
}

ContestarJson::enviar($error_txt);
