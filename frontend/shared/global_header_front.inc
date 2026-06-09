<?php


use frontend\shared\web\Posicion;
use frontend\shared\security\HashFront;

require __DIR__ . '/../../libs/vendor/autoload.php';
require __DIR__ . '/../../src/shared/load_env.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    // Para detectar el error: "Headers already sent"
    //$file = $line = null; headers_sent($file, $line); die("$file:$line");

    $sessionName = session_name();
    if ($sessionName === '') {
        $sessionName = 'PHPSESSID';
    }
    $sidFromRequest = '';
    if (!empty($_COOKIE[$sessionName]) && is_string($_COOKIE[$sessionName])) {
        $sidFromRequest = $_COOKIE[$sessionName];
    } elseif (!empty($_POST[$sessionName]) && is_string($_POST[$sessionName])
        && preg_match('/^[a-zA-Z0-9,-]{16,128}$/', $_POST[$sessionName])) {
        // fnjs_link_submenu / fnjs_update_div envían PHPSESSID en el POST AJAX.
        $sidFromRequest = $_POST[$sessionName];
    }

    if ($sidFromRequest !== '') {
        session_id($sidFromRequest);
        session_start();
        if (!isset($_SESSION['session_auth'])) {
            $_SESSION = [];
            session_regenerate_id(true);
        }
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