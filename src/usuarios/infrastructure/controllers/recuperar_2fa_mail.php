<?php

use core\ConfigDB;
use core\DBConnection;
use core\ValueObject\Uuid;
use shared\domain\ColaMailId;
use shared\domain\entity\ColaMail;
use shared\domain\repositories\ColaMailRepository;
use src\usuarios\domain\entity\Usuario;
use web\ContestarJson;

require __DIR__ . '/../../../../libs/vendor/autoload.php';

$Qusername = (string)filter_input(INPUT_POST, 'username');
$Qubicacion = (string)filter_input(INPUT_POST, 'ubicacion');
$Qesquema = (string)filter_input(INPUT_POST, 'esquema');
$Qesquema_web = (string)filter_input(INPUT_POST, 'esquema_web');
$Qurl_base = (string)filter_input(INPUT_POST, 'url_base');

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
    // para los bytea: (resources)
    $handle = $row['password'];
    if ($handle !== null) {
        $contents = stream_get_contents($handle);
        fclose($handle);
        $password = $contents;
        $row['password'] = $password;
    }
    $MiUsuario = (new Usuario())->setAllAttributes($row);
    $id_usuario = $MiUsuario->getId_usuario();
    $email = $MiUsuario->getEmailAsString();

    if (empty($email)) {
        $error_txt = _("No hay email asociado a este usuario");
    } else {
        // Recuperar código de seguridad
        $codigo = $MiUsuario->getSecret2fa()?->value();
        if ($codigo === null) {

            // 1. Generar un token aleatorio seguro
            $token_bruto = bin2hex(random_bytes(32));
            // 2. Hashearlo para guardarlo en la DB (nunca guardes el token plano)
            $token_hash = hash('sha256', $token_bruto);
            // 3. Definir expiración (ej: 15 minutos)
            $expiracion = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // 4. Update en la DB
            // UPDATE "H-dlbv".aux_usuarios SET token_recuperacion_2fa = '$token_hash', token_expiracion_2fa = '$expiracion' WHERE id_usuario = ...

            try {
                // 1. Definimos la consulta (Ojo: usa el esquema "H-dlbv" si es necesario)
                $sql = "UPDATE aux_usuarios SET token_recuperacion_2fa = :token_hash, token_expiracion_2fa = :expiracion 
                            WHERE id_usuario = :id_usuario";

                // 2. Preparamos la sentencia
                if (($oDBSt1 = $oDB->prepare($sql)) === false) {
                    throw new Exception("Error preparing SQL statement");
                }

                // 3. Generamos los datos
                $token_bruto = bin2hex(random_bytes(32));
                $token_hash = hash('sha256', $token_bruto);

                // PostgreSQL acepta bien el formato Y-m-d H:i:s
                $expiracion = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                // 4. Vinculamos parámetros
                $oDBSt1->bindParam(':token_hash', $token_hash, PDO::PARAM_STR);
                $oDBSt1->bindParam(':expiracion', $expiracion, PDO::PARAM_STR);

                // Asumo que $Qid_usuario es el ID numérico del usuario
                $oDBSt1->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

                if ($oDBSt1->execute() === false) {
                    $sClauError = 'DBRol.delGrupo.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDBSt1, $sClauError, __LINE__, __FILE__);
                    return false;
                }
                // 5. Construimos el enlace para el email
                $url = $Qurl_base . 'frontend/usuarios/controller/recovery.php';
                $link_recuperacion = $url . '?token=' . $token_bruto.'&esquema='.$esquema.'&id_usuario='.$id_usuario;

                $message = sprintf(_("Hola %s,\n\nHas solicitado recuperar tu sistema autentificación de 2 factores. Haz clik en el link: %s\n\nSaludos,\nEl equipo de administración"), $Qusername, $link_recuperacion);
            } catch (Exception $e) {
                error_log($e->getMessage());
                // Manejar el error para el usuario
            }
        } else {
            $message = sprintf(_("Hola %s,\n\nHas solicitado recuperar tu sistema autentificación de 2 factores. Tu código de seguridad para la aplicación es: %s\n\nSaludos,\nEl equipo de administración"), $Qusername, $codigo);

        }
        // Enviar email con la nueva contraseña
        $subject = _("Recuperación de código de seguridad");

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

$data['error_txt'] = $error_txt;
$data['success'] = $success;
$data['email'] = $email;

ContestarJson::enviar('', $data);
