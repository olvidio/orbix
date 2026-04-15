<?php

use src\profesores\application\ListaPorDepartamentos;
use web\ContestarJson;

$Qdl = (array)filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qfiltro = (int)filter_input(INPUT_POST, 'filtro', FILTER_DEFAULT);

$jsondata = ContestarJson::respuestaPhp('', ListaPorDepartamentos::getData($Qdl, $Qfiltro));
ContestarJson::send($jsondata);
