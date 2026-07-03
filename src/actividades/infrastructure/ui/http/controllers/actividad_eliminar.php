<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend AJAX: elimina las actividades indicadas.
 *
 * Acepta dos formas de entrada:
 *  - `sel[]` con los ids (checkboxes de seleccion masiva).
 *  - `id_activ` unico cuando se viene del planning (borrar ficha concreta).
 *
 * Si la app `procesos` esta instalada, valida el permiso `borrar` via
 * `$_SESSION['oPermActividades']`. Responde JSON {success, mensaje?}.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'eliminar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\actividades\application\ActividadEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_activ = (integer)\src\shared\domain\helpers\FilterPostGet::post('id_activ');
$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

/** @var ActividadEliminar $useCase */
$useCase = DependencyResolver::get(ActividadEliminar::class);
$error_txt = $useCase->execute([
    'sel' => $a_sel,
    'id_activ' => $Qid_activ,
]);

ContestarJson::enviar($error_txt);
