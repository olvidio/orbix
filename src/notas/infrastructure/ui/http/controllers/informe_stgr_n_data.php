<?php

use src\notas\application\InformeStgrNumerarios;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;


$error = '';
$data = [];

try {
    $QdlRaw = filter_post('dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    /** @var array<int, int|string> $Qdl */
    $Qdl = [];
    if (is_array($QdlRaw)) {
        foreach ($QdlRaw as $id) {
            if (is_string($id) && $id !== '') {
                $Qdl[] = $id;
            }
        }
    }
    $Qlista = (string)filter_post('lista');
    $lista = !empty($Qlista);

    /** @var InformeStgrNumerarios $informe */
    $informe = DependencyResolver::get(InformeStgrNumerarios::class);
    $ce_lugar = $informe->resolverCeLugar($Qdl);

    $data = $informe->calcular($Qdl, $lista, (string)$ce_lugar);
    $data['ce_lugar'] = $ce_lugar;
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
