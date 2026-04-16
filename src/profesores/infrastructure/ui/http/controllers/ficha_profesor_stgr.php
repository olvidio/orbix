<?php

use src\profesores\application\FichaProfesorStgr;
use web\ContestarJson;

$Qid_nom = (int)filter_input(INPUT_POST, 'id_nom');
$Qid_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
$Qprint = (int)filter_input(INPUT_POST, 'print');

ContestarJson::enviar('', FichaProfesorStgr::getFichaData($Qid_nom, $Qid_tabla, !empty($Qprint)));
