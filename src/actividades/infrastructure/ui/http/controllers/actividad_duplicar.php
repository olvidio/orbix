<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend AJAX: duplica la primera actividad seleccionada dentro de
 * la propia delegacion (o de la sf si el usuario tiene permiso `des`).
 * `sel[]` son cápsulas HashB (`actividad_duplicar`, `{id_activ}`).
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'duplicar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\actividades\application\ActividadDuplicar;
use src\actividades\application\ActividadMutationCtx;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$a_sel_raw = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$a_sel = ActividadMutationCtx::openSelIds($a_sel_raw, 'actividad_duplicar');
if ($a_sel === []) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var ActividadDuplicar $useCase */
$useCase = DependencyResolver::get(ActividadDuplicar::class);
$error_txt = $useCase->execute(['sel' => $a_sel]);

ContestarJson::enviar($error_txt);
