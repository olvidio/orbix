<?php

use usuarios\domain\gruposLista;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************

// Se usa al buscar:
$Qusername = (string)filter_input(INPUT_POST, 'username');

$jsondata = gruposLista::gruposLista($Qusername);

// envía una Response
ContestarJson::send($jsondata);
