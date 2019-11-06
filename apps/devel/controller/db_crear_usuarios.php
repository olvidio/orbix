<?php
use core\ConfigGlobal;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/**
 * función para generar nuestra contraseña,
 *  la cadena base que vamos a utilizar para generar la contraseña aleatoria
 *  la inicializamos tanto con letras, números y caracteres especiales.
 *   Establecemos un límite
 * @param integer $largo
 * @return string
 */
function generar_password($largo){
    $cadena_base =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $cadena_base .= '0123456789';
    // OJO no se puede usar el caracter punto y coma ';',
    // porque al crear la cadena de conexión (DSN) del PDO lo usa como separador... 
    // idem comillas.
    // idem barra de escape '\'
    $cadena_base .= '!@#%^&*()_,./<>?:[]{}|=+';
    
    $password = '';
    $limite = strlen($cadena_base) - 1;
    
    for ($i=0; $i < $largo; $i++) {
        $password .= $cadena_base[rand(0, $limite)];
        $cadena_base = str_shuffle($cadena_base);
    }
    return $password;
}

$Qregion = (string) \filter_input(INPUT_POST, 'region');
$Qdl = (string) \filter_input(INPUT_POST, 'dl');

$esquema = "$Qregion-$Qdl";
$esquema_pwd = generar_password(11);
$esquemav = $esquema.'v';
$esquemav_pwd = generar_password(11);
$esquemaf = $esquema.'f';
$esquemaf_pwd = generar_password(11);

// CREAR USUARIOS ----------------------
// Hay que pasar como parámetro el nombre de la database, que corresponde al archivo database.inc
// donde están los passwords. En este caso en importar.inc, tenermos al superadmin.
$oConfigDB = new core\ConfigDB('importar');
$config = $oConfigDB->getEsquema('public'); //de la database comun

$oConexion = new core\dbConnection($config);
$oDevelPC = $oConexion->getPDO();

$oDBRol = new core\DBRol();
$oDBRol->setDbConexion($oDevelPC);
// necesito crear los tres usuarios para dar perminsos
// comun
$oDBRol->setUser($esquema);
$oDBRol->setPwd($esquema_pwd);
$oDBRol->crearUsuario();
$oConfigDB->addEsquema('comun', $esquema, $esquema_pwd);
// sv
$oDBRol->setUser($esquemav);
$oDBRol->setPwd($esquemav_pwd);
$oDBRol->crearUsuario();
$oConfigDB->addEsquema('sv', $esquemav, $esquemav_pwd);
// sf
$oDBRol->setUser($esquemaf);
$oDBRol->setPwd($esquemaf_pwd);
$oDBRol->crearUsuario();
$oConfigDB->addEsquema('sf', $esquemaf, $esquemaf_pwd);

$archivo_conf = ConfigGlobal::DIR_PWD.'/  (comun.inc, sv.inc, sf.inc)';
echo sprintf(_("se han creado los usuarios"));
echo "<br>";
echo sprintf(_("debe copiar los siguientes usuarios y passwords en el archivo %s"),$archivo_conf);
echo "<br>";
echo "<br>";
echo "$esquema > ". htmlspecialchars($esquema_pwd). "<br>";
echo "$esquemav > ". htmlspecialchars($esquemav_pwd). "<br>";
echo "$esquemaf > ". htmlspecialchars($esquemaf_pwd). "<br>";
echo "<br>";
echo _("Ya no hace falta, pero interesa saberlo para acceder al a BD directamente.");





