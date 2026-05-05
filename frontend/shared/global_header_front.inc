<?php


use frontend\shared\web\Posicion;
use frontend\shared\security\HashFront;

require __DIR__ . '/../../libs/vendor/autoload.php';
require __DIR__ . '/../../src/shared/load_env.php';

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

require_once('frontend/usuarios/controller/login.php');


session_write_close();
// Para validar los parámetros enviados via POST o GET (si hay hash)
$oValidator = new HashFront();
$a_data = (!empty($_POST)) ? $_POST : ((isset($_GET['h'])) ? $_GET : []);
echo $oValidator->validatePost($a_data);

$oPosicion = new Posicion($_SERVER['PHP_SELF'], $_POST);