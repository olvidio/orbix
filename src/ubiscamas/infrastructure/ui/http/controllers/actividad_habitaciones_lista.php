<?php

use src\ubiscamas\application\HabitacionesCamaLista;
use web\ContestarJson;

$Qid_activ = (string)filter_input(INPUT_POST, 'id_activ');


$HabitacionCamaLista = new HabitacionesCamaLista();
$data = $HabitacionCamaLista($Qid_activ);

$error_txt = '';
// envía una Response
ContestarJson::enviar($error_txt, $data);