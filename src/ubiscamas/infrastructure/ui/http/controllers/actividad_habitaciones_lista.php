<?php
use src\ubiscamas\application\HabitacionesCamaLista;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_activ = (string)filter_input(INPUT_POST, 'id_activ');


$HabitacionCamaLista = new HabitacionesCamaLista();
$data = $HabitacionCamaLista($Qid_activ);

$error_txt = '';
// envía una Response
ContestarJson::enviar($error_txt, $data);