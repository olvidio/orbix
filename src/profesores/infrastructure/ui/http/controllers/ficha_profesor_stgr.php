<?php

use src\profesores\application\FichaProfesorStgr;
use src\shared\config\ConfigGlobal;
use src\shared\web\ContestarJson;

$Qid_nom = (int)filter_input(INPUT_POST, 'id_nom');
$Qid_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
$Qprint = (int)filter_input(INPUT_POST, 'print');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qpermiso = (string)filter_input(INPUT_POST, 'permiso');
$Qdepende = (string)filter_input(INPUT_POST, 'depende');
if (ConfigGlobal::mi_ambito() === 'rstgr') {
    $Qprint = 1;
}

$data = FichaProfesorStgr::getFichaData(
    $Qid_nom,
    $Qid_tabla,
    !empty($Qprint),
    $Qobj_pau,
    $Qpermiso,
    $Qdepende
);
$error = (string)($data['error'] ?? '');
unset($data['error']);
ContestarJson::enviar($error, $data);
