<?php

use src\profesores\application\FichaProfesorStgr;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var FichaProfesorStgr $useCase */
$useCase = DependencyResolver::get(FichaProfesorStgr::class);

$id_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom');
$id_tabla = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_tabla');
$print = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'print');
$obj_pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'obj_pau');
$permiso = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'permiso');
$depende = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'depende');
if (ConfigGlobal::mi_ambito() === 'rstgr') {
    $print = 1;
}

$data = $useCase->getFichaData(
    $id_nom,
    $id_tabla,
    $print !== 0,
    $obj_pau,
    $permiso,
    $depende
);
$errorVal = $data['error'] ?? '';
$error = is_string($errorVal) ? $errorVal : '';
unset($data['error']);
ContestarJson::enviar($error, $data);
