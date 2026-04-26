<?php

use src\encargossacd\application\EncargoLstTipoEncData;
use frontend\shared\web\ContestarJson;

$grupo = (string)(filter_input(INPUT_POST, 'grupo') ?? filter_input(INPUT_GET, 'grupo') ?? '');
$id_tipo_enc = filter_input(INPUT_POST, 'id_tipo_enc');
if ($id_tipo_enc === null) {
    $id_tipo_enc = filter_input(INPUT_GET, 'id_tipo_enc');
}

ContestarJson::enviar('', EncargoLstTipoEncData::execute($grupo, $id_tipo_enc !== null && $id_tipo_enc !== false ? (string)$id_tipo_enc : null));
