<?php

use core\ConfigDB;
use core\DBConnection;
use core\DBPropiedades;
use core\ServerConf;
use permisos\model\MyCrypt;
use src\usuarios\application\repositories\UsuarioRepository;

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
    exit(_("Sólo se puede borrar en la base de datos de pruebas"));
}
$oConfigDB = new ConfigDB('sv-e');
$UsuarioRepository = new UsuarioRepository();
foreach ($a_posibles_esquemas as $esquema) {
    $esquema .= 'v';
    $config = $oConfigDB->getEsquema($esquema);
    $oConexion = new DBConnection($config);
    $oDevelPC = $oConexion->getPDO();

    $UsuarioRepository->setoDbl($oDevelPC);
    $UsuarioRepository->setoDbl_Select($oDevelPC);

    // No cambiar el superusuario (id_role = 1).
    $aWhere = ['id_role' => 1];
    $aOperador = ['id_role' => '>'];
    $cUsuarios = $UsuarioRepository->getUsuarios($aWhere, $aOperador);
    foreach ($cUsuarios as $oUsuario) {
        $oUsuario->setoDbl($oDevelPC);
        $oUsuario->setoDbl_Select($oDevelPC);
        // poner de password el mismo login
        $login = $oUsuario->getUsuarioAsString();
        if (!empty($login) && ($login !== 'dani')) {
            $oCrypt = new MyCrypt();
            $my_passwd = $oCrypt->encode($login);
            $oUsuario->setPassword($my_passwd);
            if ($UsuarioRepository->Guardar($oUsuario) === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $UsuarioRepository->getErrorTxt();
            }
        }
    }
}