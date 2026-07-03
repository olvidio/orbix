<?php


/**
 * Endpoint backend: datos del form modificar/nuevo `TarifaUbi`.
 */

use src\actividadtarifas\application\TarifaUbiFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_item' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_item'),
    'id_ubi' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    'year' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'year'),
    'letra' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'letra'),
];

/** @var TarifaUbiFormData $useCase */
$useCase = DependencyResolver::get(TarifaUbiFormData::class);
ContestarJson::enviar('', $useCase->execute($input));
