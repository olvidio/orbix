<?php

use src\dbextern\application\VerOrbixOtraDlData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$tipo_persona = FuncTablasSupport::inputString($_POST, 'tipo_persona');
$ids_traslados_A = FuncTablasSupport::inputString($_POST, 'ids_traslados_A');

$decoded = json_decode(urldecode($ids_traslados_A), true);
/** @var list<int> $a_ids */
$a_ids = [];
if (is_array($decoded)) {
    foreach ($decoded as $id) {
        if (is_int($id) || (is_string($id) && is_numeric($id))) {
            $a_ids[] = (int)$id;
        }
    }
}

$data = DependencyResolver::get(VerOrbixOtraDlData::class)($tipo_persona, $a_ids);

ContestarJson::enviar('', $data);
