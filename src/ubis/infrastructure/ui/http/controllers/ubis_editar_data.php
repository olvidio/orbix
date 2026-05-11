<?php

use src\ubis\application\UbisEditarOpcionesData;
use src\shared\web\ContestarJson;

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qtipo_ubi = (string)filter_input(INPUT_POST, 'tipo_ubi');
$Qdl = (string)filter_input(INPUT_POST, 'dl');
$Qregion = (string)filter_input(INPUT_POST, 'region');

ContestarJson::enviar('', UbisEditarOpcionesData::execute($Qobj_pau, $Qtipo_ubi, $Qdl, $Qregion));
