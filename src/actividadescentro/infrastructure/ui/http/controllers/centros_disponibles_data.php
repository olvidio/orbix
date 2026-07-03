<?php


/**
 * Endpoint backend: devuelve los centros disponibles (candidatos) para
 * asignar como encargado de una actividad, filtrados por `tipo`
 * (sg / sr / nagd / sssc / sfsg / sfsr / sfnagd).
 */

use src\actividadescentro\application\CentrosDisponiblesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'tipo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'tipo'),
    'id_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_activ'),
    'inicio' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'inicio'),
    'fin' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'fin'),
    'f_ini_act' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_ini_act'),
];

/** @var CentrosDisponiblesData $useCase */
$useCase = DependencyResolver::get(CentrosDisponiblesData::class);
$data = $useCase->execute($input);
ContestarJson::enviar('', $data);
