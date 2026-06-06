<?php

use src\actividadescentro\application\ActivCtrShellData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$input = [
    'tipo' => input_string($_POST, 'tipo'),
    'year' => input_string($_POST, 'year'),
    'periodo' => input_string($_POST, 'periodo'),
];

/** @var ActivCtrShellData $useCase */
$useCase = DependencyResolver::get(ActivCtrShellData::class);
$data = $useCase->build($input);
ContestarJson::enviar('', $data);
