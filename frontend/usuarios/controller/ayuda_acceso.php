<?php


use core\ConfigDB;
use core\DBConnection;
use frontend\shared\OfuscarEmail;
use src\shared\ViewSrcPhtml;
use src\usuarios\domain\entity\Usuario;

/**
 * Página de ayuda para restablecer la autenticación de dos factores (2FA).
 * Esta página proporciona instrucciones detalladas para usuarios que han perdido
 * acceso a su aplicación de autenticación y necesitan restablecer su configuración de 2FA.
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
//require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************


$Qusername = (string)filter_input(INPUT_POST, 'username');
$Qubicacion = (string)filter_input(INPUT_POST, 'ubicacion');
$Qesquema = (string)filter_input(INPUT_POST, 'esquema');
$Qesquema_web = (string)filter_input(INPUT_POST, 'esquema_web');

if (empty($Qusername)) {
    exit (_("Debe ingresar un nombre de usuario"));
}

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
    $MiUsuario = (new Usuario())->setAllAttributes($row);
} else {
    exit (_("Debe ingresar un nombre de usuario válido"));
}

$error_txt = '';
$email = $MiUsuario->getEmailAsString();
if (empty($email)) {
    $error_txt = _("No hay email asociado a este usuario");
    $emailOfuscado = '';
} else {
    $emailOfuscado = OfuscarEmail::ofuscarEmailParcial($email, 3, 2);
}

$url_index = $_SERVER['HTTP_REFERER'];
$a_cosas = ['url_index' => $url_index, 'username' => $Qusername, 'ubicacion' => $Qubicacion, 'esquema' => $Qesquema];
$linkEnviarMailPasswd = 'frontend/usuarios/controller/recuperar_password.php?'.http_build_query($a_cosas);

$a_cosas = ['url_index' => $url_index, 'username' => $Qusername, 'ubicacion' => $Qubicacion, 'esquema' => $Qesquema];
$linkAyuda2FA = 'frontend/usuarios/controller/ayuda_2fa_reset.php?'.http_build_query($a_cosas);

// Mail admin. Los admin tienen role=2
$query = "SELECT usuario, email FROM aux_usuarios WHERE id_role = 2";
$mail_admin = '';
foreach ($oDB_Select->query($query) as $row) {
    if (!empty($row[1])) {
        $mail_admin .= empty($mail_admin) ? '' : ", ";
        $mail_admin .= "" . $row[1] . "";
    }
}
$mail_admin = empty($mail_admin)? _("El administrador de esta circunscripción no tiene email asociado"): $mail_admin;

$a_campos = [
    'error_txt' => $error_txt,
    'linkEnviarMailPasswd' => $linkEnviarMailPasswd,
    'emailOfuscado' => $emailOfuscado,
    'linkAyuda2FA' => $linkAyuda2FA,
    'mail_admin' => $mail_admin,
    'url_index' => $url_index,
];

$oView = new ViewSrcPhtml('frontend\usuarios\view');
$oView->renderizar('ayuda_acceso.phtml', $a_campos);