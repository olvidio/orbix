<?php

use src\notas\application\InformeStgrAgregados;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;


$error = '';
$data = [];

try {
    $QdlRaw = FilterPostGet::post('dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    /** @var array<int, int|string> $Qdl */
    $Qdl = [];
    if (is_array($QdlRaw)) {
        foreach ($QdlRaw as $id) {
            if (is_string($id) && $id !== '') {
                $Qdl[] = $id;
            }
        }
    }
    $Qlista = (string)FilterPostGet::post('lista');
    $lista = !empty($Qlista);

    /** @var InformeStgrAgregados $informe */
    $informe = DependencyResolver::get(InformeStgrAgregados::class);
    $data = $informe->calcular($Qdl, $lista);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
