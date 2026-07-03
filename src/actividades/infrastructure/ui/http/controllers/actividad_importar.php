<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend AJAX: importa las actividades seleccionadas y regenera su
 * proceso cuando la app `procesos` esta instalada.
 * Responde JSON {success, mensaje?}.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'importar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\actividades\application\ActividadImportar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

/** @var ActividadImportar $useCase */
$useCase = DependencyResolver::get(ActividadImportar::class);
$result = $useCase->execute(['sel' => $a_sel]);

if ($result['error_txt'] === '' && $result['avisos'] !== []) {
    ContestarJson::enviar('', ['avisos' => $result['avisos']]);
}

ContestarJson::enviar($result['error_txt']);
