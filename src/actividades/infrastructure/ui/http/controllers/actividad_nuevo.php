<?php

use src\shared\domain\helpers\FilterPostGet;

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
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qinom_tipo_val = (string)FilterPostGet::post('inom_tipo_val');
// Puede ser '000' > sin especificar
if (empty($Qinom_tipo_val)) {
    ContestarJson::enviar(_("debe seleccionar un tipo de actividad"));
    exit;
}

$datosActividad = [
    'id_tipo_activ' => (integer)FilterPostGet::post('id_tipo_activ'),
    'id_ubi' => (integer)FilterPostGet::post('id_ubi'),
    'num_asistentes' => (integer)FilterPostGet::post('num_asistentes'),
    'status' => (integer)FilterPostGet::post('status'),
    'id_repeticion' => (integer)FilterPostGet::post('id_repeticion'),
    'plazas' => (integer)FilterPostGet::post('plazas'),
    'tarifa' => (integer)FilterPostGet::post('id_tarifa'),
    'precio' => FilterPostGet::post('precio', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
    'dl_org' => (string)FilterPostGet::post('dl_org'),
    'nom_activ' => (string)FilterPostGet::post('nom_activ'),
    'lugar_esp' => (string)FilterPostGet::post('lugar_esp'),
    'desc_activ' => (string)FilterPostGet::post('desc_activ'),
    'f_ini' => (string)FilterPostGet::post('f_ini'),
    'f_fin' => (string)FilterPostGet::post('f_fin'),
    'tipo_horario' => (string)FilterPostGet::post('tipo_horario'),
    'observ' => (string)FilterPostGet::post('observ'),
    'nivel_stgr' => (string)FilterPostGet::post('nivel_stgr'),
    'idioma' => (string)FilterPostGet::post('idioma'),
    'observ_material' => (string)FilterPostGet::post('observ_material'),
    'h_ini' => (string)FilterPostGet::post('h_ini'),
    'h_fin' => (string)FilterPostGet::post('h_fin'),
    'publicado' => (string)FilterPostGet::post('publicado'),
];

$error_txt = '';
try {
    /** @var ActividadNueva $useCase */
    $useCase = DependencyResolver::get(ActividadNueva::class);
    $useCase->actividadNueva($datosActividad);
} catch (Exception $e) {
    $error_txt = $e->getMessage();
}

ContestarJson::enviar($error_txt);
