<?php


/**
 * Endpoint JSON: lista de fases para el tipo de actividad indicado.
 */

use src\cambios\application\CambioUsuarioObjetoPrefFasesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'objeto' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'objeto'),
    'id_tipo_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_tipo_activ'),
    'dl_propia' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'dl_propia'),
];

/** @var CambioUsuarioObjetoPrefFasesData $useCase */
$useCase = DependencyResolver::get(CambioUsuarioObjetoPrefFasesData::class);
$result = $useCase->execute($input);
$error = isset($result['error']) && is_string($result['error']) ? $result['error'] : '';
unset($result['error']);

ContestarJson::enviar($error, $result);
