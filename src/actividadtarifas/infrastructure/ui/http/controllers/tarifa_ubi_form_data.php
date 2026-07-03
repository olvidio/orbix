<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: datos del form modificar/nuevo `TarifaUbi`.
 */

use src\actividadtarifas\application\TarifaUbiFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_item' => FuncTablasSupport::inputString($_POST, 'id_item'),
    'id_ubi' => FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    'year' => FuncTablasSupport::inputInt($_POST, 'year'),
    'letra' => FuncTablasSupport::inputString($_POST, 'letra'),
];

/** @var TarifaUbiFormData $useCase */
$useCase = DependencyResolver::get(TarifaUbiFormData::class);
ContestarJson::enviar('', $useCase->execute($input));
