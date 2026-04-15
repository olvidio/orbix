<?php

use src\profesores\application\ProfesoresAsignaturaLista;
use web\ContestarJson;

$Qid_asignatura = (int)filter_input(INPUT_POST, 'id_asignatura');
$jsondata = ContestarJson::respuestaPhp('', ProfesoresAsignaturaLista::getTablaData($Qid_asignatura));
ContestarJson::send($jsondata);
