<?php

use src\shared\infrastructure\logging\GestorErrores;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use frontend\shared\OfuscarEmail;
use src\shared\web\ContestarJson;

$Qusername = (string)filter_post('username');
$Qubicacion = (string)filter_post('ubicacion');
$Qesquema = (string)filter_post('esquema');
$Qesquema_web = (string)filter_post('esquema_web');

$error_txt = '';
$data = [];

$aWhere = array('usuario' => $Qusername);
$esquema = empty($Qesquema) ? $Qesquema_web : $Qesquema;
if (empty($esquema)) {
    exit (_("Esquema no válido"));
}
$oDB_Select = null;
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
if (!($oDB_Select instanceof \PDO)) {
    exit (_("Esquema no válido"));
}
$query = "SELECT * FROM aux_usuarios WHERE usuario = :usuario";
$oDBSt = $oDB_Select->prepare($query);
if ($oDBSt === false) {
    $sClauError = 'login_obj.prepare';
    if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
        $_SESSION['oGestorErrores']->addErrorAppLastError($oDB_Select, $sClauError, (string)__LINE__, __FILE__);
    }
    return false;
}

if ($oDBSt->execute($aWhere) === false) {
    $sClauError = 'loguin_obj.execute';
    if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
        $_SESSION['oGestorErrores']->addErrorAppLastError($oDB_Select, $sClauError, (string)__LINE__, __FILE__);
    }
    return false;
}

$idioma = '';
$row = $oDBSt->fetch(\PDO::FETCH_ASSOC);
if (!is_array($row)) {
    exit (_("Debe ingresar un nombre de usuario válido"));
}
$email = is_string($row['email'] ?? null) ? $row['email'] : '';

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
$adminRows = $oDB_Select->query($query);
if ($adminRows instanceof \PDOStatement) {
    foreach ($adminRows as $adminRow) {
        if (!is_array($adminRow)) {
            continue;
        }
        $adminEmail = $adminRow[1] ?? $adminRow['email'] ?? '';
        if (!empty($adminEmail) && is_string($adminEmail)) {
            $mail_admin .= empty($mail_admin) ? '' : ", ";
            $mail_admin .= $adminEmail;
        }
    }
}
$mail_admin = empty($mail_admin) ? _("El administrador de esta circunscripción no tiene email asociado") : $mail_admin;

$data['errores'] = $errores;
$data['emailOfuscado'] = $emailOfuscado;
$data['mail_admin'] = $mail_admin;

ContestarJson::enviar($error_txt, $data);
