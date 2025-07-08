<?php
/**
 * Página para recuperar la contraseña de un usuario.
 * Genera una contraseña aleatoria, marca en la tabla del usuario que debe cambiarla
 * y envía la nueva contraseña por correo electrónico.
 */

use core\ConfigDB;
use core\DBConnection;
use core\ValueObject\Uuid;
use permisos\model\MyCrypt;
use shared\domain\ColaMailId;
use shared\domain\entity\ColaMail;
use shared\domain\repositories\ColaMailRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qusername = (string)filter_input(INPUT_POST, 'username');
$Qubicacion = (string)filter_input(INPUT_POST, 'ubicacion');
$Qesquema = (string)filter_input(INPUT_POST, 'esquema');
$Qesquema_web = (string)filter_input(INPUT_POST, 'esquema_web');
$Qurl_index = (string)filter_input(INPUT_POST, 'url_index');

$error_txt = '';
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
    $error_txt .= _("Error al preparar la consulta");
}

if (($oDBSt->execute($aWhere)) === false) {
    $sClauError = 'recuperar_password.execute';
    $_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
    $error_txt .= _("Error al ejecutar la consulta");
}

$success = false;
$email = '????';

if (empty($error_txt) && ($row = $oDBSt->fetch(\PDO::FETCH_ASSOC))) {
    $id_usuario = $row['id_usuario'];
    $email = $row['email'];

    if (empty($email)) {
        $error_txt = _("No hay email asociado a este usuario");
    } else {
        // Generar una contraseña aleatoria
        $new_password = generateRandomPassword();

        // Encriptar la contraseña
        $oCrypt = new MyCrypt();
        $hashed_password = $oCrypt->encode($new_password);

        // Actualizar la contraseña en la base de datos y marcar que debe cambiarla
        $update_query = "UPDATE aux_usuarios SET password = :password, cambio_password = TRUE WHERE id_usuario = :id_usuario";
        $stmt = $oDB->prepare($update_query);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':id_usuario', $id_usuario);

        if ($stmt->execute()) {
            // Enviar email con la nueva contraseña
            $subject = _("Recuperación de contraseña");
            $message = sprintf(_("Hola %s,\n\nHas solicitado recuperar tu contraseña. Tu nueva contraseña temporal es: %s\n\nPor razones de seguridad, deberás cambiar esta contraseña la próxima vez que inicies sesión.\n\nSaludos,\nEl equipo de administración"), $Qusername, $new_password);

            //Dirección del remitente
            $headers = "From: Aquinate <no-Reply@moneders.net>\r\n";
            // Por cambios en la política de gmail, para evitar conflictos con
            // SPF y DKIM, el From debe ser igual que el Return-path.
            // Parece que el no-Reply también lo acepta.

            // Crear un nuevo email en la cola
            $write_by = basename(__FILE__);
            $ColaMailId = new ColaMailId(Uuid::random());
            $oColaMail = new ColaMail();
            $oColaMail->setUuid_item($ColaMailId);
            $oColaMail->setMail_to($email);
            $oColaMail->setSubject($subject);
            $oColaMail->setMessage($message);
            $oColaMail->setWrited_by($write_by);

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
        } else {
            $error_txt = _("Error al actualizar la contraseña");
        }
    }
} else {
    $error_txt = _("No se encontró ningún usuario con ese nombre");
}

// Función para generar una contraseña aleatoria
function generateRandomPassword($length = 10)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';

    // Asegurar que la contraseña tenga al menos una letra mayúscula, una minúscula, un número y un carácter especial
    $password .= $chars[random_int(26, 51)]; // Mayúscula
    $password .= $chars[random_int(0, 25)]; // Minúscula
    $password .= $chars[random_int(52, 61)]; // Número
    $password .= $chars[random_int(62, strlen($chars) - 1)]; // Carácter especial

    // Completar el resto de la contraseña
    for ($i = 4; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }

    // Mezclar los caracteres para que no sigan un patrón predecible
    return str_shuffle($password);
}


$data['error_txt'] = $error_txt;
$data['success'] = $success;
$data['email'] = $email;

ContestarJson::enviar('', $data);