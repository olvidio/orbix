<?php

use core\ConfigDB;
use core\DBConnection;
use core\DBPropiedades;
use core\ServerConf;
use permisos\model\MyCrypt;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\value_objects\Password;
use web\ContestarJson;
use function core\is_true;

$error_txt = '';
$data = [];

// todos los esquemas
$oDBPropiedades = new DBPropiedades();
$a_posibles_esquemas = $oDBPropiedades->array_posibles_esquemas(TRUE, TRUE);
// sólo para pruebas-sv-e
$isDocker = FALSE;
if  (preg_match('/(.*?)\.docker/',ServerConf::SERVIDOR )) {
    $isDocker = TRUE;
}
if (ServerConf::WEBDIR !== 'pruebas' && !$isDocker ) {
    ContestarJson::enviar(_("Sólo se puede borrar en la base de datos de pruebas"), $data);
    return; // no continuar
}
$oConfigDB = new ConfigDB('sv-e');
$UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);

$actualizados = 0;
$error_txt = '';
foreach ($a_posibles_esquemas as $esquema) {
    $esquema .= 'v';
    try {
        $config = $oConfigDB->getEsquema($esquema);
    } catch (Exception $e) {
        $error_txt .= $e->getMessage() . "\n";
        continue;
    }
    $oConexion = new DBConnection($config);
    $oDevelPC = $oConexion->getPDO();

    $UsuarioRepository->setoDbl($oDevelPC);
    $UsuarioRepository->setoDbl_Select($oDevelPC);

    // No cambiar el superusuario (id_role = 1).
    $aWhere = ['id_role' => 1];
    $aOperador = ['id_role' => '>'];
    $cUsuarios = $UsuarioRepository->getUsuarios($aWhere, $aOperador);
    foreach ($cUsuarios as $oUsuario) {
        // poner de password el mismo login
        $login = $oUsuario->getUsuarioAsString();
        if (!empty($login) && ($login !== 'dani')) {
            $oCrypt = new MyCrypt();
            $my_passwd = $oCrypt->encode($login);
            $oUsuario->setPasswordVo(new Password($my_passwd));
            if ($UsuarioRepository->Guardar($oUsuario) === false) {
                $error_txt .= (_("hay un error, no se ha guardado")) . "\n" . $UsuarioRepository->getErrorTxt() . "\n";
            } else {
                $actualizados++;
            }
        }
    }
}

$data['actualizados'] = $actualizados;
ContestarJson::enviar($error_txt, $data);