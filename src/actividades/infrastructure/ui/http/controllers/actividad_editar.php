<?php
/**
 * Endpoint backend AJAX: guarda la edicion de una actividad existente.
 * Si se cambia la delegacion de/ a la propia dl regenera el proceso, y propaga
 * `plazas` a la tabla de plazas de la propia dl cuando aplica.
 * Responde JSON {success, mensaje?}.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'editar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\actividades\application\ActividadEditar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ActividadEditar $useCase */
$useCase = DependencyResolver::get(ActividadEditar::class);
$result = $useCase->execute($_POST);

if (isset($result['tipo_error'])) {
    ContestarJson::enviar($result['error_txt']);
    exit;
}

ContestarJson::enviar($result['error_txt']);
