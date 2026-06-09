<?php

use src\notas\application\InformeStgrProfesores;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;


$error = '';
$data = [];

try {
    $Qlista = (string)filter_input(INPUT_POST, 'lista');
    $lista = !empty($Qlista);

    /** @var InformeStgrProfesores $informe */
    $informe = DependencyResolver::get(InformeStgrProfesores::class);
    $data = $informe->calcular($lista);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
