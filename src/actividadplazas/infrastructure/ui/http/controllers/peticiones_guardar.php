<?php


/**
 * Endpoint backend: guarda las peticiones de una persona+tipo
 * (borra las anteriores y crea las nuevas en orden).
 */

use src\actividadplazas\application\PeticionesGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_nom' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom'),
    'sactividad' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'sactividad'),
    'actividades' => \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'actividades'),
];

/** @var PeticionesGuardar $useCase */
$useCase = DependencyResolver::get(PeticionesGuardar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
