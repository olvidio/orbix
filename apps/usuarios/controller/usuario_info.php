<?php

use usuarios\model\entity\Usuario;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

$error_txt = '';

// grupos:
$oGesUsuarioGrupo = new usuarios\model\entity\GestorUsuarioGrupo();
$oListaGrupos = $oGesUsuarioGrupo->getUsuariosGrupos(array('id_usuario' => $Qid_usuario));
$i = 0;
$txt = '';
foreach ($oListaGrupos as $oUsuarioGrupo) {
    $i++;
    $oGrupo = new usuarios\model\entity\Grupo($oUsuarioGrupo->getId_grupo());
    if ($i > 1) $txt .= ", ";
    $txt .= $oGrupo->getUsuario();
}

// datos personales usuario
$oUsuario = new Usuario(array('id_usuario' => $Qid_usuario));
$usuario = $oUsuario->getUsuario();
$pass = $oUsuario->getPassword();
$email = $oUsuario->getEmail();

$data['grupos_txt'] = $txt;
$data['usuario'] = $usuario;
$data['pass'] = $pass;
$data['email'] = $email;

ContestarJson::enviar($error_txt, $data);

