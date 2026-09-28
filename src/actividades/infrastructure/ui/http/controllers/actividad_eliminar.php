<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend AJAX: elimina las actividades indicadas.
 *
 * Acepta `sel[]` con cápsulas HashB emitidas por fila en el listado
 * (`actividad_eliminar`, contexto `{id_activ}`). Ya no acepta `id_activ` plano.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'eliminar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\actividades\application\ActividadEliminar;
use src\actividades\application\ActividadMutationCtx;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$a_sel_raw = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$a_sel = ActividadMutationCtx::openSelIds($a_sel_raw, 'actividad_eliminar');
if ($a_sel_raw !== [] && $a_sel === []) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var ActividadEliminar $useCase */
$useCase = DependencyResolver::get(ActividadEliminar::class);
$error_txt = $useCase->execute([
    'sel' => $a_sel,
    'id_activ' => 0,
]);

ContestarJson::enviar($error_txt);
