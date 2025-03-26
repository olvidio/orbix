<?php


use web\Hash;
use web\Posicion;

require __DIR__ . '/../../libs/vendor/autoload.php';

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

require_once('apps/permisos/controller/login_obj.php');


session_write_close();
// Para validar los parámetros enviados via POST
$oValidator = new Hash();
echo $oValidator->validatePost($_POST);

$oPosicion = new Posicion($_SERVER['PHP_SELF'], $_POST);

