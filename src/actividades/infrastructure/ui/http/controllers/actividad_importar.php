<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend AJAX: importa las actividades seleccionadas y regenera su
 * proceso cuando la app `procesos` esta instalada.
 * `sel[]` son cápsulas HashB (`actividad_importar`, `{id_activ}`).
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'importar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\actividades\application\ActividadImportar;
use src\actividades\application\ActividadMutationCtx;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$a_sel_raw = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$a_sel = ActividadMutationCtx::openSelIds($a_sel_raw, 'actividad_importar');
if ($a_sel_raw !== [] && $a_sel === []) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var ActividadImportar $useCase */
$useCase = DependencyResolver::get(ActividadImportar::class);
$result = $useCase->execute(['sel' => $a_sel]);

if ($result['error_txt'] === '' && $result['avisos'] !== []) {
    ContestarJson::enviar('', ['avisos' => $result['avisos']]);
}

ContestarJson::enviar($result['error_txt']);
