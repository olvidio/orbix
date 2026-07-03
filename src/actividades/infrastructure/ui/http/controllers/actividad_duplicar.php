<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend AJAX: duplica la primera actividad seleccionada dentro de
 * la propia delegacion (o de la sf si el usuario tiene permiso `des`).
 * Responde JSON {success, mensaje?}.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'duplicar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\actividades\application\ActividadDuplicar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

/** @var ActividadDuplicar $useCase */
$useCase = DependencyResolver::get(ActividadDuplicar::class);
$error_txt = $useCase->execute(['sel' => $a_sel]);

ContestarJson::enviar($error_txt);
