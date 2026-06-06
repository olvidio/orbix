<?php
/**
 * Endpoint JSON: lista de fases para el tipo de actividad indicado.
 */

use src\cambios\application\CambioUsuarioObjetoPrefFasesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;

require_once 'frontend/shared/global_header_front.inc';

$input = [
    'objeto' => input_string($_POST, 'objeto'),
    'id_tipo_activ' => input_string($_POST, 'id_tipo_activ'),
    'dl_propia' => input_string($_POST, 'dl_propia'),
];

/** @var CambioUsuarioObjetoPrefFasesData $useCase */
$useCase = DependencyResolver::get(CambioUsuarioObjetoPrefFasesData::class);
$result = $useCase->execute($input);
$error = isset($result['error']) && is_string($result['error']) ? $result['error'] : '';
unset($result['error']);

ContestarJson::enviar($error, $result);
