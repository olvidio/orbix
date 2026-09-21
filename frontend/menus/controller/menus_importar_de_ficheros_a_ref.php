<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\FrontBootstrap;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashF;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$seguro = filter_input(INPUT_POST, 'seguro', FILTER_VALIDATE_INT);
if ($seguro === false || $seguro === null) {
    $seguro = filter_input(INPUT_GET, 'seguro', FILTER_VALIDATE_INT);
}
$todos = filter_input(INPUT_POST, 'todos', FILTER_VALIDATE_INT);
if ($todos === false || $todos === null) {
    $todos = filter_input(INPUT_GET, 'todos', FILTER_VALIDATE_INT);
}

$seguro = ($seguro === false || $seguro === null || $seguro === 0) ? 2 : $seguro;
$todos = ($todos === false || $todos === null || $todos === 0) ? 2 : $todos;

$data = PostRequest::getDataFromUrl('/src/menus/menus_importar_de_ficheros_a_ref', [
    'seguro' => $seguro,
    'todos' => $todos,
]);

$selfUrl = AppUrlConfig::browserUrlFromAppRelative(
    'frontend/menus/controller/menus_importar_de_ficheros_a_ref.php'
);
$aCampos = [
    'data' => $data,
    'url_confirmar' => $selfUrl,
    'parametros_confirmar' => HashF::add_hash(['seguro' => 1], $selfUrl),
    'parametros_confirmar_todas' => HashF::add_hash(['seguro' => 1, 'todos' => 1], $selfUrl),
];

$oView = new ViewNewPhtml('frontend\menus\controller');
$oView->renderizar('menus_importar_de_ficheros_a_ref.phtml', $aCampos);
