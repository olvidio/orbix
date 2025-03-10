<?php

use usuarios\model\entity\Grupo;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qid_usuario = (string)filter_input(INPUT_POST, 'id_usuario');

$oGrupo = new Grupo(array('id_usuario' => $Qid_usuario));
$nombre = $oGrupo->getUsuario();

$error_txt='';
$data['nombre'] = $nombre;


ContestarJson::enviar($error_txt, $data);