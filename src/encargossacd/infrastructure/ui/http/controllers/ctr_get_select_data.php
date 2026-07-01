<?php

use src\encargossacd\application\EncargoCtrSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoCtrSelectData $useCase */
$useCase = DependencyResolver::get(EncargoCtrSelectData::class);


$id_ubi = filter_post('id_ubi');
if ($id_ubi === null) {
    $id_ubi = filter_get('id_ubi');
}
$filtro_ctr = filter_post('filtro_ctr');
if ($filtro_ctr === null) {
    $filtro_ctr = filter_get('filtro_ctr');
}
$id_zona = filter_post('id_zona');
if ($id_zona === null) {
    $id_zona = filter_get('id_zona');
}
// `action` opcional: el handler onchange del <select>. Ausente => default
// `fnjs_ver_ficha()` (ctr_ficha). Una vista sin ficha (encargo_ver) lo envía
// vacío para que el select no tenga onchange.
$action = filter_post('action');
if ($action === null) {
    $action = filter_get('action');
}

ContestarJson::enviar('', $useCase->execute(
    $id_ubi !== null && $id_ubi !== false ? (int)$id_ubi : 0,
    $filtro_ctr !== null && $filtro_ctr !== false ? (int)$filtro_ctr : 0,
    $id_zona !== null && $id_zona !== false ? (int)$id_zona : 0,
    $action !== null && $action !== false ? $action : null
));
