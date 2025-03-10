<?php

use core\ConfigGlobal;
use usuarios\domain\usuarioEliminar;
use usuarios\model\entity\GestorGrupo;
use usuarios\model\entity\GestorUsuarioGrupo;
use usuarios\model\entity\Grupo;
use usuarios\model\entity\Role;
use usuarios\model\entity\Usuario;
use usuarios\model\entity\UsuarioGrupo;
use web\ContestarJson;
use web\Hash;
use web\Lista;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$sfsv = ConfigGlobal::mi_sfsv();
$error_txt = '';

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qid_grupo = (integer)filter_input(INPUT_POST, 'id_grupo');

// elimino el grupo de permisos al usuario.
$oUsuarioGrupo = new UsuarioGrupo(array('id_usuario' => $Qid_usuario, 'id_grupo' => $Qid_grupo));
if ($oUsuarioGrupo->DBEliminar() === false) {
        $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $oUsuarioGrupo->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');