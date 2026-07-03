<?php

use src\notas\application\ActaSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$error = '';
$data = [];
try {
    $data = (DependencyResolver::get(ActaSelectData::class))->execute([
        'titulo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'titulo'),
        'acta' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'acta'),
        'mes_fin_stgr' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'mes_fin_stgr', 6),
    ]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
