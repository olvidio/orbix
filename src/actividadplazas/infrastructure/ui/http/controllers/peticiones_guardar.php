<?php
/**
 * Endpoint backend: guarda las peticiones de una persona+tipo
 * (borra las anteriores y crea las nuevas en orden).
 */

use src\actividadplazas\application\PeticionesGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_string_list;

$input = [
    'id_nom' => input_int($_POST, 'id_nom'),
    'sactividad' => input_string($_POST, 'sactividad'),
    'actividades' => input_string_list($_POST, 'actividades'),
];

/** @var PeticionesGuardar $useCase */
$useCase = DependencyResolver::get(PeticionesGuardar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
