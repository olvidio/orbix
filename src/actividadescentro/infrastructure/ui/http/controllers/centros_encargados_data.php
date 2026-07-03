<?php


/**
 * Endpoint backend: devuelve los centros encargados actuales de una
 * actividad en un array serializable, junto con los flags de permiso.
 */

use src\actividadescentro\application\CentrosEncargadosData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_activ'),
    'id_tipo_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_tipo_activ'),
    'dl_org' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'dl_org'),
];

/** @var CentrosEncargadosData $useCase */
$useCase = DependencyResolver::get(CentrosEncargadosData::class);
$data = $useCase->execute($input);
ContestarJson::enviar('', $data);
