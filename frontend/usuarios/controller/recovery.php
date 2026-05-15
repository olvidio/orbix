<?php


use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use frontend\shared\web\UrlBaseProject;


require __DIR__ . '/../../../libs/vendor/autoload.php';

$Qtoken = (string)filter_input(INPUT_GET, 'token');
$Qid_usuario = (integer)filter_input(INPUT_GET, 'id_usuario');
$Qesquema = (string)filter_input(INPUT_GET, 'esquema');


$hash_recibido = HashFront('sha256', $Qtoken);

// Buscas en la DB un usuario donde:
// token_recuperacion_2fa == $hash_recibido
// AND token_expiracion_2fa > NOW()

$esquema = $Qesquema;
$sfsv = 0;
$oDB = null;
$ubicacion = (string) getenv('UBICACION');
$private = (string) getenv('PRIVATE');
$useSfDb = ($ubicacion === 'sf' || $private === 'sf');

if (substr($esquema, -1) === 'v') {
    $sfsv = 1;
    $oConfigDB = new ConfigDB('sv-e');
    $config = $oConfigDB->getEsquema($esquema);
    $oConexion = new DBConnection($config);
    $oDB = $oConexion->getPDO();
} elseif (substr($esquema, -1) === 'f') {
    if ($useSfDb) {
        try {
            $sfsv = 2;
            $oConfigDB = new ConfigDB('sf-e');
            $config = $oConfigDB->getEsquema($esquema);
            $oConexion = new DBConnection($config);
            $oDB = $oConexion->getPDO();
        } catch (\Throwable $e) {
            $esquema = substr($esquema, 0, -1);
            $sfsv = 0;
        }
    } else {
        $esquema = substr($esquema, 0, -1);
    }
}

if ($oDB === null) {
    $oConfigDB = new ConfigDB('comun_select');
    $config = $oConfigDB->getEsquema($esquema);
    $oConexion = new DBConnection($config);
    $oDB = $oConexion->getPDO();
}

// Buscar el usuario en la base de datos
$query = "SELECT * FROM aux_usuarios WHERE id_usuario = $Qid_usuario AND token_recuperacion_2fa = '$hash_recibido' AND token_expiracion_2fa > NOW()";
$oDBSt = $oDB->Query($query);

$usuario_encontrado = $oDBSt->fetch();


if ($usuario_encontrado) {
    // EL MOMENTO CLAVE:
    // 1. Pones has_2fa = false
    // 2. Pones secret_2fa = NULL
    // 3. Limpias los campos del token para que no se reusen
    // 4. Inicias sesión y rediriges a la página de "Configurar Nuevo QR"

    $id_usuario = $usuario_encontrado['id_usuario'];
    $id_role = $usuario_encontrado['id_role'];
    $sql_reset = 'UPDATE "H-dlbv".aux_usuarios 
                      SET has_2fa = false, 
                          secret_2fa = NULL, 
                          token_recuperacion_2fa = NULL, 
                          token_expiracion_2fa = NULL 
                      WHERE id_usuario = :id';

    $oDB->prepare($sql_reset)->execute([':id' => $id_usuario]);

    // 5. Loguear al usuario automáticamente (opcional pero recomendado)
    // $_SESSION['usuario_id'] = $id_usuario;
    // $_SESSION['auth_completada'] = true;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        // Para detectar el error: "Headers already sent"
        //$file = $line = null; headers_sent($file, $line); die("$file:$line");

        if (!empty($_COOKIE["PHPSESSID"])) {
            session_id($_COOKIE['PHPSESSID']);
            session_start();
        } else {
            // Configure timeout to 30 minutes
            $timeout = 1800;
            $maxlifetime = time() + $timeout;

            // Set the maxlifetime of session
            ini_set("session.gc_maxlifetime", $timeout);

            // Also set the session cookie timeout
            ini_set("session.cookie_lifetime", $timeout);

            //$domain = ConfigGlobal::getDomain();
            // Now start the session
            session_set_cookie_params([
                'lifetime' => $maxlifetime,
                'Secure' => false,
                'HttpOnly' => true,
                'SameSite' => 'Strict',
                //'Domain' => $domain
            ]);
            session_start();
        }
    }

    $session_auth = array(
        'id_usuario' => $id_usuario,
        //'MiUsuario' => $MiUsuario,
        'sfsv' => $sfsv,
        'id_role' => $id_role,
        //'role_pau' => $role_pau,
        //'username' => $_POST['username'],
        //'password' => $_POST['password'],
        'esquema' => $esquema,
        //'perms_activ' => $perms_activ,
        //'mi_oficina' => $mi_oficina,
        //'mi_oficina_menu' => $mi_oficina_menu,
        'expire' => true,
        //'mail' => $mail,
        //'idioma' => $idioma,
        //'ordenApellidos' => $ordenApellidos,
        //'mi_id_schema' => $id_schema,
    );
    $_SESSION['session_auth'] = $session_auth;
    $_SESSION['msg_2fa'] = _("Por razones de seguridad, debe configurar la autenticación de dos factores (2FA).");

    $mi_ruta = 'frontend/usuarios/controller/usuario_form_2fa.php';
    $url_base = UrlBaseProject::getUrlBase();
    $url_backend = $url_base . $mi_ruta;

    echo "<h2>El doble factor ha sido desactivado con éxito.</h2>";
    echo "<p>Por seguridad, debes configurar uno nuevo ahora mismo.</p>";
    echo "<a href=\"$url_backend\" style=\"button\">Configurar nuevo 2FA</a>";

} else {
    // El token no coincide o ya expiró (más de 15 min)
    echo "El enlace ha caducado o es inválido. Por favor, solicita uno nuevo.";
}
