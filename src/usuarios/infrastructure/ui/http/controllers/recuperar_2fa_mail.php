<?php

use core\ConfigDB;
use core\DBConnection;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\domain\entity\ColaMail;
use src\shared\domain\value_objects\ColaMailId;
use src\shared\domain\value_objects\Uuid;
use src\usuarios\domain\entity\Usuario;
use web\ContestarJson;

$Qusername = (string)filter_input(INPUT_POST, 'username');
$Qubicacion = (string)filter_input(INPUT_POST, 'ubicacion');
$Qesquema = (string)filter_input(INPUT_POST, 'esquema');
$Qesquema_web = (string)filter_input(INPUT_POST, 'esquema_web');
$Qurl_index = (string)filter_input(INPUT_POST, 'url_index');

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
    $MiUsuario = Usuario::fromArray($row);
    $id_usuario = $MiUsuario->getId_usuario();
    $email = $MiUsuario->getEmailAsString();

    if (empty($email)) {
        $error_txt = _("No hay email asociado a este usuario");
    } else {
        // Recuperar código de seguridad
        $codigo = $MiUsuario->getSecret2faVo()->value();

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

        $oColaMailRepository = $GLOBALS['container']->get(ColaMailRepositoryInterface::class);
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

$data['error_txt'] = $error_txt;
$data['success'] = $success;
$data['email'] = $email;

ContestarJson::enviar('', $data);
