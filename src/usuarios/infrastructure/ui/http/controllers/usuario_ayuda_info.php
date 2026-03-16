<?php

use core\ConfigDB;
use core\DBConnection;
use frontend\shared\OfuscarEmail;
use src\usuarios\domain\entity\Usuario;
use web\ContestarJson;

//

$Qusername = (string)filter_input(INPUT_POST, 'username');
$Qubicacion = (string)filter_input(INPUT_POST, 'ubicacion');
$Qesquema = (string)filter_input(INPUT_POST, 'esquema');
$Qesquema_web = (string)filter_input(INPUT_POST, 'esquema_web');

$error_txt = '';
$data = [];

$aWhere = array('usuario' => $Qusername);
$esquema = empty($Qesquema) ? $Qesquema_web : $Qesquema;
if (substr($esquema, -1) === 'v') {
    $sfsv = 1;
    $oConfigDB = new ConfigDB('sv-e_select');
    $config = $oConfigDB->getEsquema($esquema);
    $oConexion = new DBConnection($config);
    $oDB_Select = $oConexion->getPDO();

}
if (substr($esquema, -1) === 'f') {
    $sfsv = 2;
    $oConfigDB = new ConfigDB('sf-e');
    $config = $oConfigDB->getEsquema($esquema);
    $oConexion = new DBConnection($config);
    $oDB_Select = $oConexion->getPDO();
}
$query = "SELECT * FROM aux_usuarios WHERE usuario = :usuario";
if (($oDBSt = $oDB_Select->prepare($query)) === false) {
    $sClauError = 'login_obj.prepare';
    $_SESSION['oGestorErrores']->addErrorAppLastError($oDB_Select, $sClauError, __LINE__, __FILE__);
    return false;
}

if (($oDBSt->execute($aWhere)) === false) {
    $sClauError = 'loguin_obj.execute';
    $_SESSION['oGestorErrores']->addErrorAppLastError($oDB_Select, $sClauError, __LINE__, __FILE__);
    return false;
}

$idioma = '';
if ($row = $oDBSt->fetch(\PDO::FETCH_ASSOC)) {
    $email = $row['email'];
} else {
    exit (_("Debe ingresar un nombre de usuario válido"));
}

$errores = '';
if (empty($email)) {
    $errores = _("No hay email asociado a este usuario");
    $emailOfuscado = '';
} else {
    $emailOfuscado = OfuscarEmail::ofuscarEmailParcial($email, 3, 2);
}

// Mail admin. Los admin tienen role=2
$query = "SELECT usuario, email FROM aux_usuarios WHERE id_role = 2";
$mail_admin = '';
foreach ($oDB_Select->query($query) as $row) {
    if (!empty($row[1])) {
        $mail_admin .= empty($mail_admin) ? '' : ", ";
        $mail_admin .= "" . $row[1];
    }
}
$mail_admin = empty($mail_admin) ? _("El administrador de esta circunscripción no tiene email asociado") : $mail_admin;

$data['errores'] = $errores;
$data['emailOfuscado'] = $emailOfuscado;
$data['mail_admin'] = $mail_admin;

ContestarJson::enviar($error_txt, $data);

