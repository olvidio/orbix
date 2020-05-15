<?php
use permisos\model\MyCrypt;
use usuarios\model\entity\GestorUsuario;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

// No cambiar el superusuario (id_role = 1).
$aWhere = ['id_role' => 1];
$aOperador = ['id_role' => '>'];
$gesUsuarios = new GestorUsuario();
$cUsuarios = $gesUsuarios->getUsuarios($aWhere,$aOperador);
foreach ($cUsuarios as $oUsuario) {
    $oUsuario->DBCarregar();
    // poner de password el mismo login
    $login = $oUsuario->getUsuario();
    if (!empty($login)){
        $oCrypt = new MyCrypt();
        $my_passwd=$oCrypt->encode($login);
        $oUsuario->setPassword($my_passwd);
        if ($oUsuario->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n".$oUsuario->getErrorTxt();
        }
    }
}