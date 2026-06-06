<?php
/**
 * Endpoint backend AJAX: cambia el tipo de una actividad existente.
 * Regenera el proceso asociado si la app `procesos` esta instalada.
 * Responde JSON {success, mensaje?}.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'cambiar_tipo').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\actividades\application\ActividadCambiarTipo;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ActividadCambiarTipo $useCase */
$useCase = DependencyResolver::get(ActividadCambiarTipo::class);
$result = $useCase->execute($_POST);

if (isset($result['tipo_error'])) {
    ContestarJson::enviar($result['error_txt']);
    exit;
}

ContestarJson::enviar($result['error_txt']);
