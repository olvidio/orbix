<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend AJAX: marca como publicadas las actividades seleccionadas.
 * Responde JSON {success, mensaje?}.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'publicar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\actividades\application\ActividadPublicar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$a_sel = (array)FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

/** @var ActividadPublicar $useCase */
$useCase = DependencyResolver::get(ActividadPublicar::class);
$error_txt = $useCase->execute(['sel' => $a_sel]);

ContestarJson::enviar($error_txt);
