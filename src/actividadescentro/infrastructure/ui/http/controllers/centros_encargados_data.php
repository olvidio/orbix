<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: devuelve los centros encargados actuales de una
 * actividad en un array serializable, junto con los flags de permiso.
 */

use src\actividadescentro\application\CentrosEncargadosData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_activ' => FuncTablasSupport::inputInt($_POST, 'id_activ'),
    'id_tipo_activ' => FuncTablasSupport::inputString($_POST, 'id_tipo_activ'),
    'dl_org' => FuncTablasSupport::inputString($_POST, 'dl_org'),
];

/** @var CentrosEncargadosData $useCase */
$useCase = DependencyResolver::get(CentrosEncargadosData::class);
$data = $useCase->execute($input);
ContestarJson::enviar('', $data);
