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

$Qinom_tipo_val = (string)\src\shared\domain\helpers\FilterPostGet::post('inom_tipo_val');
// Puede ser '000' > sin especificar
if (empty($Qinom_tipo_val)) {
    ContestarJson::enviar(_("debe seleccionar un tipo de actividad"));
    exit;
}

$datosActividad = [
    'id_tipo_activ' => (integer)\src\shared\domain\helpers\FilterPostGet::post('id_tipo_activ'),
    'id_ubi' => (integer)\src\shared\domain\helpers\FilterPostGet::post('id_ubi'),
    'num_asistentes' => (integer)\src\shared\domain\helpers\FilterPostGet::post('num_asistentes'),
    'status' => (integer)\src\shared\domain\helpers\FilterPostGet::post('status'),
    'id_repeticion' => (integer)\src\shared\domain\helpers\FilterPostGet::post('id_repeticion'),
    'plazas' => (integer)\src\shared\domain\helpers\FilterPostGet::post('plazas'),
    'tarifa' => (integer)\src\shared\domain\helpers\FilterPostGet::post('id_tarifa'),
    'precio' => \src\shared\domain\helpers\FilterPostGet::post('precio', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
    'dl_org' => (string)\src\shared\domain\helpers\FilterPostGet::post('dl_org'),
    'nom_activ' => (string)\src\shared\domain\helpers\FilterPostGet::post('nom_activ'),
    'lugar_esp' => (string)\src\shared\domain\helpers\FilterPostGet::post('lugar_esp'),
    'desc_activ' => (string)\src\shared\domain\helpers\FilterPostGet::post('desc_activ'),
    'f_ini' => (string)\src\shared\domain\helpers\FilterPostGet::post('f_ini'),
    'f_fin' => (string)\src\shared\domain\helpers\FilterPostGet::post('f_fin'),
    'tipo_horario' => (string)\src\shared\domain\helpers\FilterPostGet::post('tipo_horario'),
    'observ' => (string)\src\shared\domain\helpers\FilterPostGet::post('observ'),
    'nivel_stgr' => (string)\src\shared\domain\helpers\FilterPostGet::post('nivel_stgr'),
    'idioma' => (string)\src\shared\domain\helpers\FilterPostGet::post('idioma'),
    'observ_material' => (string)\src\shared\domain\helpers\FilterPostGet::post('observ_material'),
    'h_ini' => (string)\src\shared\domain\helpers\FilterPostGet::post('h_ini'),
    'h_fin' => (string)\src\shared\domain\helpers\FilterPostGet::post('h_fin'),
    'publicado' => (string)\src\shared\domain\helpers\FilterPostGet::post('publicado'),
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
