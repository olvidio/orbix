<?php
/**
 * Endpoint backend: devuelve los sacd encargados actuales de una
 * actividad en un array serializable, junto con los flags de permiso.
 */

use src\actividadessacd\application\SacdsEncargadosData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_activ' => input_int($_POST, 'id_activ'),
    'id_tipo_activ' => input_string($_POST, 'id_tipo_activ'),
    'dl_org' => input_string($_POST, 'dl_org'),
];

/** @var SacdsEncargadosData $useCase */
$useCase = DependencyResolver::get(SacdsEncargadosData::class);
ContestarJson::enviar('', $useCase->execute($input));
