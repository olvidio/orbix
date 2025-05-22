<?php

use src\usuarios\application\repositories\GrupoRepository;
use src\usuarios\domain\entity\Grupo;
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
$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

$GrupoRepository = new GrupoRepository();
if (empty($Qid_usuario)) {
    $id_usuario_new = $GrupoRepository->getNewId();
    $oGrupo = new Grupo();
    $oGrupo->setId_usuario($id_usuario_new);
} else {
    $oGrupo = $GrupoRepository->findById($Qid_usuario);
}
$oGrupo->setUsuario($Qusuario);

if ($GrupoRepository->Guardar($oGrupo) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $GrupoRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');