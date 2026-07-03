<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\shared\config\ServerConf;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\PasswordHasher;
use src\usuarios\domain\value_objects\Password;
use src\shared\web\ContestarJson;
$error_txt = '';
$data = [];

// todos los esquemas
$oDBPropiedades = new DBPropiedades();
$a_posibles_esquemas = $oDBPropiedades->array_posibles_esquemas(TRUE, TRUE);
if (!is_array($a_posibles_esquemas)) {
    ContestarJson::enviar(_("No se pudieron obtener esquemas"), $data);
    return;
}
// sólo para pruebas-sv-e
$isDocker = FALSE;
if  (preg_match('/(.*?)\.docker/',ServerConf::SERVIDOR )) {
    $isDocker = TRUE;
}
$webdir = getenv('WEBDIR') !== false ? (string)getenv('WEBDIR') : ServerConf::WEBDIR;
if ($webdir !== 'pruebas' && !$isDocker) {
    ContestarJson::enviar(_("Sólo se puede borrar en la base de datos de pruebas"), $data);
    return; // no continuar
}
$oConfigDB = new ConfigDB('sv-e');
$UsuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);

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

    $UsuarioRepository->setoDbl_select($oDevelPC);

    // No cambiar el superusuario (id_role = 1).
    $aWhere = ['id_role' => 1];
    $aOperador = ['id_role' => '>'];
    $cUsuarios = $UsuarioRepository->getUsuarios($aWhere, $aOperador);
    foreach ($cUsuarios as $oUsuario) {
        // poner de password el mismo login
        $login = $oUsuario->getUsuarioAsString();
        if (!empty($login) && ($login !== 'dani')) {
            $oCrypt = new PasswordHasher();
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