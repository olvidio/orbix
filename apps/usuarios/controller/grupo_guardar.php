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

$Qusuario = (string)filter_input(INPUT_POST, 'usuario');

$error_txt = '';
if (empty($Qusuario)) {
    $error_txt .= _("debe poner un nombre");
}
$Qid_role = (integer)filter_input(INPUT_POST, 'id_role');
$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

$oGrupo = new Grupo(array('id_usuario' => $Qid_usuario));
$oGrupo->setUsuario($Qusuario);
$oGrupo->setid_role($Qid_role);

if ($oGrupo->DBGuardar() === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $oGrupo->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');