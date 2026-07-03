<?php

use src\notas\application\InformeStgrProfesores;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;


$error = '';
$data = [];

try {
    $Qlista = (string)\src\shared\domain\helpers\FilterPostGet::post('lista');
    $lista = !empty($Qlista);

    /** @var InformeStgrProfesores $informe */
    $informe = DependencyResolver::get(InformeStgrProfesores::class);
    $data = $informe->calcular($lista);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
