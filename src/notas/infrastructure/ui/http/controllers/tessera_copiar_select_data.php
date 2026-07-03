<?php

use src\notas\application\TesseraCopiarSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$error = '';
$data = [];
try {
    $id_nom = FuncTablasSupport::inputInt($_POST, 'id_nom');
    if ($id_nom === 0) {
        throw new \RuntimeException(_("Se requiere id_nom"));
    }
    $data = (DependencyResolver::get(TesseraCopiarSelectData::class))->execute($id_nom);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
