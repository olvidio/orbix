<?php
/**
 * Endpoint backend: devuelve los centros disponibles (candidatos) para
 * asignar como encargado de una actividad, filtrados por `tipo`
 * (sg / sr / nagd / sssc / sfsg / sfsr / sfnagd).
 */

use src\actividadescentro\application\CentrosDisponiblesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$input = [
    'tipo' => input_string($_POST, 'tipo'),
    'id_activ' => input_int($_POST, 'id_activ'),
    'inicio' => input_string($_POST, 'inicio'),
    'fin' => input_string($_POST, 'fin'),
    'f_ini_act' => input_string($_POST, 'f_ini_act'),
];

/** @var CentrosDisponiblesData $useCase */
$useCase = DependencyResolver::get(CentrosDisponiblesData::class);
$data = $useCase->execute($input);
ContestarJson::enviar('', $data);
