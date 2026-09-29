<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend AJAX: marca como publicadas las actividades seleccionadas.
 * `sel[]` son cápsulas HashB (`actividad_publicar`, `{id_activ}`).
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'publicar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\actividades\application\ActividadMutationCtx;
use src\actividades\application\ActividadPublicar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$a_sel_raw = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$a_sel = ActividadMutationCtx::openSelIds($a_sel_raw, 'actividad_publicar');
if ($a_sel_raw !== [] && $a_sel === []) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var ActividadPublicar $useCase */
$useCase = DependencyResolver::get(ActividadPublicar::class);
$error_txt = $useCase->execute(['sel' => $a_sel]);

ContestarJson::enviar($error_txt);
