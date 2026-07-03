<?php

use src\dbextern\application\VerTrasladosData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$tipo_persona = FuncTablasSupport::inputString($_POST, 'tipo_persona');
$ids_traslados = FuncTablasSupport::inputString($_POST, 'ids_traslados');

$decoded = json_decode(urldecode($ids_traslados), true);
/** @var list<int> $a_ids_traslados */
$a_ids_traslados = [];
if (is_array($decoded)) {
    foreach ($decoded as $id) {
        if (is_int($id) || (is_string($id) && is_numeric($id))) {
            $a_ids_traslados[] = (int)$id;
        }
    }
}

$data = DependencyResolver::get(VerTrasladosData::class)($tipo_persona, $a_ids_traslados);

ContestarJson::enviar('', $data);
