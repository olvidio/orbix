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
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qinom_tipo_val = (string)filter_post('inom_tipo_val');
// Puede ser '000' > sin especificar
if (empty($Qinom_tipo_val)) {
    ContestarJson::enviar(_("debe seleccionar un tipo de actividad"));
    exit;
}

$datosActividad = [
    'id_tipo_activ' => (integer)filter_post('id_tipo_activ'),
    'id_ubi' => (integer)filter_post('id_ubi'),
    'num_asistentes' => (integer)filter_post('num_asistentes'),
    'status' => (integer)filter_post('status'),
    'id_repeticion' => (integer)filter_post('id_repeticion'),
    'plazas' => (integer)filter_post('plazas'),
    'tarifa' => (integer)filter_post('id_tarifa'),
    'precio' => filter_post('precio', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
    'dl_org' => (string)filter_post('dl_org'),
    'nom_activ' => (string)filter_post('nom_activ'),
    'lugar_esp' => (string)filter_post('lugar_esp'),
    'desc_activ' => (string)filter_post('desc_activ'),
    'f_ini' => (string)filter_post('f_ini'),
    'f_fin' => (string)filter_post('f_fin'),
    'tipo_horario' => (string)filter_post('tipo_horario'),
    'observ' => (string)filter_post('observ'),
    'nivel_stgr' => (string)filter_post('nivel_stgr'),
    'idioma' => (string)filter_post('idioma'),
    'observ_material' => (string)filter_post('observ_material'),
    'h_ini' => (string)filter_post('h_ini'),
    'h_fin' => (string)filter_post('h_fin'),
    'publicado' => (string)filter_post('publicado'),
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
