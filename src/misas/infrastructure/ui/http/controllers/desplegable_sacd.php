<?php

use src\misas\application\DesplegableSacdData;
use frontend\shared\web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$Qid_sacd = (int)filter_input(INPUT_POST, 'id_sacd');
$Qseleccion = (int)filter_input(INPUT_POST, 'seleccion');
$Qdia = (string)filter_input(INPUT_POST, 'dia');

ContestarJson::enviar('', DesplegableSacdData::getData($Qid_zona, $Qid_sacd, $Qseleccion, $Qdia));
