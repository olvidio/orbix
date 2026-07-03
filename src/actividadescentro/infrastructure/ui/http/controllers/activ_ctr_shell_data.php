<?php

use src\actividadescentro\application\ActivCtrShellData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$input = [
    'tipo' => FuncTablasSupport::inputString($_POST, 'tipo'),
    'year' => FuncTablasSupport::inputString($_POST, 'year'),
    'periodo' => FuncTablasSupport::inputString($_POST, 'periodo'),
];

/** @var ActivCtrShellData $useCase */
$useCase = DependencyResolver::get(ActivCtrShellData::class);
$data = $useCase->build($input);
ContestarJson::enviar('', $data);
