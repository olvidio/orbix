<?php

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;
use core\DBPropiedades;
use core\ServerConf;
use permisos\model\MyCrypt;
use usuarios\model\entity\GestorUsuario;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

// todos los esquemas
$oDBPropiedades = new DBPropiedades();
$a_posibles_esquemas = $oDBPropiedades->array_posibles_esquemas(TRUE, TRUE);
// sólo para pruebas-sv-e
if (ServerConf::WEBDIR !== 'pruebas') {
    exit(_("Sólo se peuede borrar en la base de datos de pruebas"));
}
$oConfigDB = new ConfigDB('sv-e');
foreach ($a_posibles_esquemas as $esquema) {
    $esquema .= 'v';
    $config = $oConfigDB->getEsquema($esquema);
    $oConexion = new DBConnection($config);
    $oDevelPC = $oConexion->getPDO();

    $gesUsuarios = new GestorUsuario();
    $gesUsuarios->setoDbl($oDevelPC);
    $gesUsuarios->setoDbl_Select($oDevelPC);

    // No cambiar el superusuario (id_role = 1).
    $aWhere = ['id_role' => 1];
    $aOperador = ['id_role' => '>'];
    $cUsuarios = $gesUsuarios->getUsuarios($aWhere, $aOperador);
    foreach ($cUsuarios as $oUsuario) {
        $oUsuario->setoDbl($oDevelPC);
        $oUsuario->setoDbl_Select($oDevelPC);
        $oUsuario->DBCarregar();
        // poner de password el mismo login
        $login = $oUsuario->getUsuario();
        if (!empty($login) && ($login !== 'dani')) {
            $oCrypt = new MyCrypt();
            $my_passwd = $oCrypt->encode($login);
            $oUsuario->setPassword($my_passwd);
            if ($oUsuario->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oUsuario->getErrorTxt();
            }
        }
    }
}