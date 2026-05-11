<?php

use src\misas\application\UpdateIniciales;
use src\shared\web\ContestarJson;

$Qid_sacd = (int)filter_input(INPUT_POST, 'id_sacd');
$Qiniciales = (string)filter_input(INPUT_POST, 'iniciales');
$Qcolor = (string)filter_input(INPUT_POST, 'color');

$error = UpdateIniciales::execute($Qid_sacd, $Qiniciales, $Qcolor);

ContestarJson::enviar($error, ['id_sacd' => $Qid_sacd]);
