<?php

use src\dbextern\application\VerDesaparecidosDeOrbixData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$tipo_persona = FuncTablasSupport::inputString($_POST, 'tipo_persona');
$ids_desaparecidos_de_orbix = FuncTablasSupport::inputString($_POST, 'ids_desaparecidos_de_orbix');

$decoded = json_decode(urldecode($ids_desaparecidos_de_orbix), true);
/** @var list<int> $a_ids */
$a_ids = [];
if (is_array($decoded)) {
    foreach ($decoded as $id) {
        if (is_int($id) || (is_string($id) && is_numeric($id))) {
            $a_ids[] = (int)$id;
        }
    }
}

$data = DependencyResolver::get(VerDesaparecidosDeOrbixData::class)($tipo_persona, $a_ids);

ContestarJson::enviar('', $data);
