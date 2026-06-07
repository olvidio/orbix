<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\profesores\application\FichaProfesorStgr;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var FichaProfesorStgr $useCase */
$useCase = DependencyResolver::get(FichaProfesorStgr::class);

$id_nom = input_int($_POST, 'id_nom');
$id_tabla = input_string($_POST, 'id_tabla');
$print = input_int($_POST, 'print');
$obj_pau = input_string($_POST, 'obj_pau');
$permiso = input_string($_POST, 'permiso');
$depende = input_string($_POST, 'depende');
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
