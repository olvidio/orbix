<?php

use src\profesores\application\ProfesoresAsignaturaLista;
use web\ContestarJson;

$Qid_asignatura = (int)filter_input(INPUT_POST, 'id_asignatura');
ContestarJson::enviar('', ProfesoresAsignaturaLista::getTablaData($Qid_asignatura));
