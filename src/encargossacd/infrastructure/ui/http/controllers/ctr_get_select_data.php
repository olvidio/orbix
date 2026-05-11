<?php

use src\encargossacd\application\EncargoCtrSelectData;
use src\shared\web\ContestarJson;

$id_ubi = filter_input(INPUT_POST, 'id_ubi');
if ($id_ubi === null) {
    $id_ubi = filter_input(INPUT_GET, 'id_ubi');
}
$filtro_ctr = filter_input(INPUT_POST, 'filtro_ctr');
if ($filtro_ctr === null) {
    $filtro_ctr = filter_input(INPUT_GET, 'filtro_ctr');
}
$id_zona = filter_input(INPUT_POST, 'id_zona');
if ($id_zona === null) {
    $id_zona = filter_input(INPUT_GET, 'id_zona');
}

ContestarJson::enviar('', EncargoCtrSelectData::execute(
    $id_ubi !== null && $id_ubi !== false ? (int)$id_ubi : 0,
    $filtro_ctr !== null && $filtro_ctr !== false ? (int)$filtro_ctr : 0,
    $id_zona !== null && $id_zona !== false ? (int)$id_zona : 0
));
