<?php

use core\ConfigDB;
use core\DBConnection;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\OfuscarEmail;
use src\usuarios\domain\entity\Usuario;

// vengo por $GET
$_POST = empty($_POST) ? $_GET : $_POST;

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


$Qusername = (string)filter_input(INPUT_GET, 'username');
$Qubicacion = (string)filter_input(INPUT_GET, 'ubicacion');
$Qesquema = (string)filter_input(INPUT_GET, 'esquema');
$Qesquema_web = (string)filter_input(INPUT_GET, 'esquema_web');
$Qurl_index = (string)filter_input(INPUT_GET, 'url_index');

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
}

$email = $MiUsuario->getEmailAsString();
if (empty($email)) {
    $error_txt = _("No hay email asociado a este usuario");
    $emailOfuscado = '';
} else {
    $emailOfuscado = OfuscarEmail::ofuscarEmailParcial($email, 3, 2);
}

$a_cosas = ['url_index' => $Qurl_index, 'username' => $Qusername, 'ubicacion' => $Qubicacion, 'esquema' => $Qesquema];
$linkEnviarMail2fa = 'recuperar_2fa.php?'.http_build_query($a_cosas);

$a_campos = [
    'linkEnviarMail2fa' => $linkEnviarMail2fa,
    'emailOfuscado' => $emailOfuscado,
    'url_index' => $Qurl_index,
];

$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('ayuda_2fa_reset.phtml', $a_campos);
