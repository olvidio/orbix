<?php

namespace permisos\controller;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;
use core\DBPropiedades;
use core\ViewPhtml;
use permisos\model\MyCrypt;
use src\usuarios\domain\entity\Usuario;
use src\usuarios\domain\Verify2fa;


// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************
// 
// FIN de  Cabecera global de URL de controlador ********************************

function cambiar_idioma($idioma = '')
{
    if (empty($idioma)) {
        // Si no está determinado en las preferencias, miro el del navegador
        if (empty($_SESSION['session_auth']['idioma'])) {
            // mirar el idioma del navegador
            if (!empty($_SERVER["HTTP_ACCEPT_LANGUAGE"])) { # Verificamos que el visitante haya designado algún idioma
                $a_idiomas = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]); # Convertimos HTTP_ACCEPT_LANGUAGE en array
                /* Recorremos el array hasta que encontramos un idioma del visitante que coincida con los idiomas
                en que está disponible nuestra web */
                $numero_de_idiomas = count($a_idiomas);
                for ($i = 0; $i < $numero_de_idiomas; $i++) {
                    if (!isset($idioma)) {
                        if (substr($a_idiomas[$i], 0, 2) === "ca") {
                            $idioma = "ca_ES.UTF-8";
                        }
                        if (substr($a_idiomas[$i], 0, 2) === "es") {
                            $idioma = "es_ES.UTF-8";
                        }
                        if (substr($a_idiomas[$i], 0, 2) === "en") {
                            $idioma = "en_US.UTF-8";
                        }
                        if (substr($a_idiomas[$i], 0, 2) === "de") {
                            $idioma = "de_DE.UTF-8";
                        }
                        //if (substr($a_idiomas[$i], 0, 2) == "en"){$idioma = "en";}
                        //if (substr($a_idiomas[$i], 0, 2) == "fr"){$idioma = "fr";}
                    }
                }
            }
        } else {
            $idioma = $_SESSION['session_auth']['idioma'];
        }
        # Si no hemos encontrado ningún idioma que nos convenga, mostramos la web en el idioma por defecto
        if (!isset($idioma)) {
            $idioma = $_SESSION['oConfig']->getIdioma_default();
        }
    }
    //$idioma=  str_replace('UTF-8', 'utf8', $idioma);
    $domain = "orbix";
//	echo "dir: ".ConfigGlobal::$dir_languages."<br>";
//	echo "domain: $domain, id: $idioma<br>";
    setlocale(LC_ALL, "");
    putenv("LC_ALL=''");
    putenv("LANGUAGE=");

    setlocale(LC_ALL, $idioma);
    putenv("LC_ALL={$idioma}");
    putenv("LANG={$idioma}");

    bindtextdomain($domain, ConfigGlobal::$dir_languages);
    textdomain($domain);
    bind_textdomain_codeset($domain, 'UTF-8');
}

// APLICACIONES POSIBLES
function getAppsPosibles()
{
    $oConfigDB = new ConfigDB('comun_select');
    $config = $oConfigDB->getEsquema('public');
    $oConexion = new DBConnection($config);
    $oDBP_Select = $oConexion->getPDO();
    $sQuery = "SELECT * FROM m0_apps";
    $a_apps = [];
    foreach ($oDBP_Select->query($sQuery) as $aDades) {
        $nom = $aDades['nom'];
        $a_apps[$nom] = $aDades['id_app'];
    }
    return $a_apps;
}

// MÓDULOS POSIBLES
function getModsPosibles()
{
    $oConfigDB = new ConfigDB('comun_select');
    $config = $oConfigDB->getEsquema('public');
    $oConexion = new DBConnection($config);
    $oDBP_Select = $oConexion->getPDO();
    $sQuery = "SELECT * FROM m0_modulos";
    $a_mods = [];
    $a_mods_req = [];
    $a_apps_req = [];
    foreach ($oDBP_Select->query($sQuery) as $aDades) {
        $id_mod = $aDades['id_mod'];
        $nom = $aDades['nom'];
        $mods_req = $aDades['mods_req'];
        $apps_req = $aDades['apps_req'];
        $a_mods[$id_mod] = array('nom' => $nom, 'mods_req' => $mods_req, 'apps_req' => $apps_req);
    }
    return $a_mods;
}

// APLICACIONES INSTALADAS EN LA DL
function getModsInstalados($oDB_Select)
{
    $a_mods = getModsPosibles();
    $sQuery = "SELECT * FROM m0_mods_installed_dl WHERE active = 't'";
    $a_mods_installed = [];
    foreach ($oDB_Select->query($sQuery) as $aDades) {
        $id_mod = $aDades['id_mod'];
        $nom_mod = $a_mods[$id_mod]['nom'];
        $a_mods_installed[$id_mod] = $nom_mod;
    }
    return $a_mods_installed;
}

function getAppsMods($id_mod)
{
    $apps = [];
    $a_mods = getModsPosibles();
    $ajson = $a_mods[$id_mod]['mods_req'];
    if (preg_match('/^{(.*)}$/', $ajson, $matches)) {
        if (!empty($matches[1])) {
            $apps_installed = [];
            $mod_in = str_getcsv($matches[1]);
            foreach ($mod_in as $mod) {
                $apps_installed[] = getApps($mod);
            }
            $apps = array_merge(...array_values($apps_installed));
        }
    }
    return $apps;
}

function getApps($id_mod)
{
    $apps = [];
    $a_mods = getModsPosibles();
    $ajson = $a_mods[$id_mod]['apps_req'];
    if (preg_match('/^{(.*)}$/', $ajson, $matches)) {
        $app_in = str_getcsv($matches[1]);

        foreach ($app_in as $app) {
            array_push($apps, $app);
        }
    }
    return $apps;
}

function logout($username, $ubicacion, $idioma, $esquema, $error, $esquema_web = '')
{
    $oDBPropiedades = new DBPropiedades();
    $a_campos = [];
    $a_campos['error'] = $error;
    $a_campos['ubicacion'] = $ubicacion;
    $a_campos['esquema_web'] = $esquema_web;
    $a_campos['DesplRegiones'] = $oDBPropiedades->posibles_esquemas($esquema);
    $a_campos['idioma'] = $idioma;
    $a_campos['username'] = $username;
    $a_campos['url'] = ConfigGlobal::getWeb();
    $oView = new ViewPhtml(__NAMESPACE__);
    $oView->renderizar('login_form2.phtml', $a_campos);
}

// ara a global_obj. $GLOBALS['oPerm'] = new permisos\PermDl();
//$GLOBALS['oPermActiv'] = new PermActiv;
$esquema_web = getenv('ESQUEMA');
$ubicacion = getenv('UBICACION');
$private = getenv('PRIVATE');

$_SESSION['sfsv'] = $ubicacion;

if (!empty($esquema_web)) {
    $oDBPropiedades = new DBPropiedades();
    $a_posibles_esquemas = $oDBPropiedades->array_posibles_esquemas(FALSE, TRUE);
    if (!in_array($esquema_web, $a_posibles_esquemas)) {
        $msg = sprintf(_("No existe este equema: %s"), $esquema_web);
        die ($msg);
    }
}

if (!isset($_SESSION['session_auth'])) {
    //la segunda vez tengo el nombre y el password
    $idioma = '';
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $_SESSION['private'] = $private;
        $mail = '';

        $aWhere = array('usuario' => $_POST['username']);
        $esquema = empty($_POST['esquema']) ? $esquema_web : $_POST['esquema'];
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
        $password_db = null;
        $oCrypt = new MyCrypt();
        $oDBSt->bindColumn('password', $password_db, \PDO::PARAM_STR);
        if ($row = $oDBSt->fetch(\PDO::FETCH_ASSOC)) {
            $row['password'] = $password_db;
            $MiUsuario = (new Usuario())->setAllAttributes($row);

            // Verificación de contraseña exitosa
            if ($oCrypt->encode($_POST['password'], $password_db) === $password_db) {

                $expire = ""; //de momento, para utilizar mas adelante...
                // Para obligar a cambiar el password
                if ($MiUsuario->isCambio_password() || $_POST['password'] === '1ªVegada') {
                    $expire = 1;
                }

                // Verificar el código 2FA si está habilitado para el usuario
                $has_2fa = $row['has_2fa'] ?? false;

                if ($has_2fa) {
                    // Si el usuario tiene 2FA habilitado, verificar el código
                    if (empty($_POST['verification_code'])) {
                        $error = 3; // Código de error para 2FA requerido
                        logout($_POST['username'], $ubicacion, $idioma, $esquema, $error, $esquema_web);
                        die();
                    }

                    // Verificar el código 2FA
                    $verification_code = $_POST['verification_code'];
                    $user_secret = $row['secret_2fa']; // Clave secreta almacenada para el usuario

                    // Verificar el código TOTP
                    if (!Verify2fa::verify_2fa_code($verification_code, $user_secret)) {
                        $error = 4; // Código de error para código 2FA inválido
                        logout($_POST['username'], $ubicacion, $idioma, $esquema, $error, $esquema_web);
                        die();
                    }
                }

                // Continuar con el proceso de login normal
                $id_usuario = $row['id_usuario'];
                $id_role = $row['id_role'];
                // ... resto del código existente
                $oConfigDB = new ConfigDB('comun_select');
                $config = $oConfigDB->getEsquema('public');
                $oConexion = new DBConnection($config);
                $oDBCP_Select = $oConexion->getPDO();
                $queryr = "SELECT * FROM aux_roles WHERE id_role = $id_role";
                if (($oDBPSt = $oDBCP_Select->query($queryr)) === false) {
                    $sClauError = 'login_obj.prepare';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDBCP_Select, $sClauError, __LINE__, __FILE__);
                    return false;
                }
                $row2 = $oDBPSt->fetch(\PDO::FETCH_ASSOC);
                $role_pau = $row2['pau'];

                // Para la MDZ, solo roles DMZ
                if (ConfigGlobal::is_dmz()) {
                    $role_dmz = $row2['dmz'];
                    if (empty($role_dmz)) {
                        $error = 2;
                        logout($_POST['username'], $ubicacion, $idioma, $esquema, $error, $esquema_web);
                        die();
                    }
                }

                // si no tiene mail interior, cojo el exterior.
                $mail = empty($mail) ? $row['email'] : $mail;

                $a_mods = getModsPosibles();
                $a_apps = getAppsPosibles();
                $app_installed = [];

                $a_mods_installed = getModsInstalados($oDB_Select);
                foreach ($a_mods_installed as $id_mod => $param) {
                    $app[] = getAppsMods($id_mod);
                    $app[] = getApps($id_mod);
                }
                $app_installed = array_merge(...array_values($app));
                $app_installed = array_unique($app_installed);

                $perms_activ = '';
                $mi_oficina = '';
                $mi_oficina_menu = '';

                // Idioma
                $query_idioma = sprintf("select * from web_preferencias where id_usuario = '%s' and tipo = '%s' ", $id_usuario, "idioma");
                $oDBStI = $oDB_Select->query($query_idioma);
                $row = $oDBStI->fetch(\PDO::FETCH_ASSOC);
                $idioma = ($row === FALSE) ? '' : $row['preferencia'];
                if (!isset($idioma)) {
                    $idioma = '';
                }

                // ordenApellidos
                $query_ordenApellidos = sprintf("select * from web_preferencias where id_usuario = '%s' and tipo = '%s' ", $id_usuario, "ordenApellidos");
                $oDBStoA = $oDB_Select->query($query_ordenApellidos);
                $row = $oDBStoA->fetch(\PDO::FETCH_ASSOC);
                $ordenApellidos = ($row === FALSE) ? '' : $row['preferencia'];

                // Id_schema
                $oDBPropiedades = new DBPropiedades();
                $id_schema = $oDBPropiedades->getIdSchema($esquema);

                //si existe, registro la sesion con los permisos
                if (!isset($_SESSION['session_auth'])) {
                    $session_auth = array(
                        'id_usuario' => $id_usuario,
                        'MiUsuario' => $MiUsuario,
                        'sfsv' => $sfsv,
                        'id_role' => $id_role,
                        'role_pau' => $role_pau,
                        'username' => $_POST['username'],
                        'password' => $_POST['password'],
                        'esquema' => $esquema,
                        'perms_activ' => $perms_activ,
                        'mi_oficina' => $mi_oficina,
                        'mi_oficina_menu' => $mi_oficina_menu,
                        'expire' => $expire,
                        'mail' => $mail,
                        'idioma' => $idioma,
                        'ordenApellidos' => $ordenApellidos,
                        'mi_id_schema' => $id_schema,
                    );
                    $_SESSION['session_auth'] = $session_auth;
                }
                //si existe, registro la sesion con la configuración
                if (!isset($_SESSION['config'])) {
                    $session_config = array(
                        'id_role' => $id_role,
                        'role_pau' => $role_pau,
                        'username' => $_POST['username'],
                        'password' => $_POST['password'],
                        'perms_activ' => $perms_activ,
                        'mi_oficina' => $mi_oficina,
                        'mi_oficina_menu' => $mi_oficina_menu,
                        'expire' => $expire,
                        'mail' => $mail,
                        'idioma' => $idioma,
                        'app_installed' => $app_installed,
                        'mod_installed' => $a_mods_installed,
                        'a_apps' => $a_apps,
                        'a_mods' => $a_mods,
                    );
                    $_SESSION['config'] = $session_config;
                }
                /* para la traducción. Después de registrar session_auth */
                cambiar_idioma();
                /* a ver si memoriza el esquema al que entro */
                $time_expire_cookie = time() + (86400 * 30);  // 86400 = 1 day
                $arr_cookie_options = [
                    'expires' => $time_expire_cookie,
                    'path' => '/',
                    //'domain' => '.example.com', // leading dot for compatibility or use subdomain
                    'secure' => false,     // true or false (true solamente en https)
                    'httponly' => true,    // or false
                    'samesite' => 'Strict' // None || Lax  || Strict
                ];
                setcookie('esquema', $esquema, $arr_cookie_options);
                setcookie('idioma', $idioma, $arr_cookie_options);

                /* Hacer que vaya a la pagina de inicio.
                 * No funciona, */
                //header("Location: ".ConfigGlobal::getWeb(), true, 301);
            } else {
                $error = 1;
                logout($_POST['username'], $ubicacion, $idioma, $esquema, $error, $esquema_web);
                die();
            }
        } else {
            $error = 1;
            logout($_POST['username'], $ubicacion, $idioma, $esquema, $error, $esquema_web);
            die();
        }
    } else { // el primer cop
        $esquema = (!isset($_COOKIE["esquema"])) ? "" : $_COOKIE["esquema"];
        $idioma = (!isset($_COOKIE["idioma"])) ? "" : $_COOKIE["idioma"];
        cambiar_idioma($idioma);
        $error = 0;
        logout('', $ubicacion, $idioma, $esquema, $error, $esquema_web);
        die();
    }
} else {
    // ya esta registrado;
    /**
     *  parece que los cambios con setlocale son para el proceso,
     *  no para session ni multithreaded, por tanto hay que hacerlo cada vez
     *  para la traducción
     */
    cambiar_idioma();
}

if (!isset($_SESSION['session_go_to'])) {
    $_SESSION['session_go_to'] = "a";
    // para que la primera vez vaya a la pagina de inicio personalizada (se mira en index.php):
    $primera = 1;
}
