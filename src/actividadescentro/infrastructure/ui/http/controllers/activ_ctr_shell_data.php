<?php

use src\actividadescentro\application\ActivCtrShellData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'tipo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'tipo'),
    'year' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'year'),
    'periodo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'periodo'),
];

/** @var ActivCtrShellData $useCase */
$useCase = DependencyResolver::get(ActivCtrShellData::class);
$data = $useCase->build($input);
ContestarJson::enviar('', $data);
