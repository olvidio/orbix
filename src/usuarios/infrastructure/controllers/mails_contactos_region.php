<?php

// INICIO Cabecera global de URL de controlador *********************************
use src\usuarios\application\usuariosRegionContactos;
use web\ContestarJson;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************

$Qregion = (string)filter_input(INPUT_POST, 'region');

$error_txt = '';

$MailsRegion = new usuariosRegionContactos();
$data = $MailsRegion->usuariosRegionContactos($Qregion);

$error_txt = $data['error_txt'] ?? '';

// envía una Response
ContestarJson::enviar($error_txt, $data);