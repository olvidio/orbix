<?php

use src\encargossacd\application\EncargoCtrSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var EncargoCtrSelectData $useCase */
$useCase = DependencyResolver::get(EncargoCtrSelectData::class);


$id_ubi = \src\shared\domain\helpers\FilterPostGet::post('id_ubi');
if ($id_ubi === null) {
    $id_ubi = \src\shared\domain\helpers\FilterPostGet::get('id_ubi');
}
$filtro_ctr = \src\shared\domain\helpers\FilterPostGet::post('filtro_ctr');
if ($filtro_ctr === null) {
    $filtro_ctr = \src\shared\domain\helpers\FilterPostGet::get('filtro_ctr');
}
$id_zona = \src\shared\domain\helpers\FilterPostGet::post('id_zona');
if ($id_zona === null) {
    $id_zona = \src\shared\domain\helpers\FilterPostGet::get('id_zona');
}
// `action` opcional: el handler onchange del <select>. Ausente => default
// `fnjs_ver_ficha()` (ctr_ficha). Una vista sin ficha (encargo_ver) lo envía
// vacío para que el select no tenga onchange.
$action = \src\shared\domain\helpers\FilterPostGet::post('action');
if ($action === null) {
    $action = \src\shared\domain\helpers\FilterPostGet::get('action');
}

ContestarJson::enviar('', $useCase->execute(
    $id_ubi !== null && $id_ubi !== false ? (int)$id_ubi : 0,
    $filtro_ctr !== null && $filtro_ctr !== false ? (int)$filtro_ctr : 0,
    $id_zona !== null && $id_zona !== false ? (int)$id_zona : 0,
    $action !== null && $action !== false ? $action : null
));
