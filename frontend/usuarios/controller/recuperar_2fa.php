<?php

use core\ConfigDB;
use core\DBConnection;
use core\ValueObject\Uuid;
use frontend\shared\model\ViewNewPhtml;
use shared\domain\ColaMailId;
use shared\domain\entity\ColaMail;
use shared\domain\repositories\ColaMailRepository;
use src\usuarios\domain\entity\Usuario;

/**
 * Página para recuperar la contraseña de un usuario.
 * Genera una contraseña aleatoria, marca en la tabla del usuario que debe cambiarla
 * y envía la nueva contraseña por correo electrónico.
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qusername = (string)filter_input(INPUT_GET, 'username');
$Qubicacion = (string)filter_input(INPUT_GET, 'ubicacion');
$Qesquema = (string)filter_input(INPUT_GET, 'esquema');
$Qesquema_web = (string)filter_input(INPUT_GET, 'esquema_web');
$Qurl_index = (string)filter_input(INPUT_GET, 'url_index');

// Si no hay username, redirigir a la página de ayuda
if (empty($Qusername)) {
    header("Location: ayuda_acceso.php");
    exit;
}

$aWhere = array('usuario' => $Qusername);
$esquema = empty($Qesquema) ? $Qesquema_web : $Qesquema;
if (substr($esquema, -1) === 'v') {
    $sfsv = 1;
    $oConfigDB = new ConfigDB('sv-e');
    $config = $oConfigDB->getEsquema($esquema);
    $oConexion = new DBConnection($config);
    $oDB = $oConexion->getPDO();

}
if (substr($esquema, -1) === 'f') {
    $sfsv = 2;
    $oConfigDB = new ConfigDB('sf-e');
    $config = $oConfigDB->getEsquema($esquema);
    $oConexion = new DBConnection($config);
    $oDB = $oConexion->getPDO();
}

// Buscar el usuario en la base de datos
$query = "SELECT * FROM aux_usuarios WHERE usuario = :usuario";
if (($oDBSt = $oDB->prepare($query)) === false) {
    $sClauError = 'recuperar_password.prepare';
    $_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
    return false;
}

if (($oDBSt->execute($aWhere)) === false) {
    $sClauError = 'recuperar_password.execute';
    $_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
    return false;
}

$error_txt = '';
$success = false;

if ($row = $oDBSt->fetch(\PDO::FETCH_ASSOC)) {
    $MiUsuario = (new Usuario())->setAllAttributes($row);
    $id_usuario = $MiUsuario->getId_usuario();
    $email = $MiUsuario->getEmailAsString();

    if (empty($email)) {
        $error_txt = _("No hay email asociado a este usuario");
    } else {
        // Recuperar código de seguridad
        $codigo = $MiUsuario->getSecret2fa();

        // Enviar email con la nueva contraseña
        $subject = _("Recuperación de código de seguridad");
        $message = sprintf(_("Hola %s,\n\nHas solicitado recuperar tu sistema autentificación de 2 factores. Tu código de seguridad para la aplicación es: %s\n\nSaludos,\nEl equipo de administración"), $Qusername, $codigo);

        // Crear un nuevo email en la cola
        $ColaMailId = new ColaMailId(Uuid::random());
        $oColaMail = new ColaMail();
        $oColaMail->setUuid_item($ColaMailId);
        $oColaMail->setMail_to($email);
        $oColaMail->setSubject($subject);
        $oColaMail->setMessage($message);
        $oColaMail->setWrited_by('system');

        // Guardar el email en la cola
        $oConfigDB = new ConfigDB('comun');
        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $oDBPC = $oConexion->getPDO();

        $oColaMailRepository = new ColaMailRepository();
        $oColaMailRepository->setoDbl($oDBPC);
        if ($oColaMailRepository->Guardar($oColaMail)) {
            $success = true;
        } else {
            $error_txt = _("Error al enviar el correo electrónico");
        }
    }
} else {
    $error_txt = _("No se encontró ningún usuario con ese nombre");
}

// Preparar los datos para la vista
$a_campos = [
    'error_txt' => $error_txt,
    'success' => $success,
    'username' => $Qusername,
    'esquema' => $Qesquema,
    'email' => $email,
    'url_index' => $Qurl_index,
];

// Renderizar la vista
$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('recuperar_2fa.phtml', $a_campos);