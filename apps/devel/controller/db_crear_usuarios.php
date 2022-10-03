<?php

use core\ConfigGlobal;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/**
 * función para generar nuestra contraseña,
 *  la cadena base que vamos a utilizar para generar la contraseña aleatoria
 *  la inicializamos tanto con letras, números y caracteres especiales.
 *   Establecemos un límite
 * @param integer $largo
 * @return string
 */
function generar_password($largo)
{
    $cadena_base = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $cadena_base .= '0123456789';
    // OJO no se puede usar el carácter punto y coma ';',
    // porque al crear la cadena de conexión (DSN) del PDO lo usa como separador... 
    // idem comillas.
    // idem barra de escape '\'
    $cadena_base .= '!@#%^&*()_,./<>?:[]{}|=+';

    $password = '';
    $limite = strlen($cadena_base) - 1;

    for ($i = 0; $i < $largo; $i++) {
        $password .= $cadena_base[rand(0, $limite)];
        $cadena_base = str_shuffle($cadena_base);
    }
    return $password;
}

$Qregion = (string)\filter_input(INPUT_POST, 'region');
$Qdl = (string)\filter_input(INPUT_POST, 'dl');

$esquema = "$Qregion-$Qdl";
$esquema_pwd = generar_password(11);
$esquemav = $esquema . 'v';
$esquemav_pwd = generar_password(11);
$esquemaf = $esquema . 'f';
$esquemaf_pwd = generar_password(11);

// CREAR USUARIOS ----------------------
// Hay que pasar como parámetro el nombre de la database, que corresponde al archivo database.inc
// donde están los passwords. En este caso en importar.inc, tenemos al superadmin.
$oConfigDB = new core\ConfigDB('importar');
//coge los valores de public: 1.la database comun; 2.nombre superusuario; 3.pasword superusuario;
$config = $oConfigDB->getEsquema('public');

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

// Con las bases de datos en distintos servidores, hay que ir cambiando la conexión:
// sv
$oConfigDB = new core\ConfigDB('importar');
//coge los valores de public: 1.la database sv; 2.nombre superusuario; 3.pasword superusuario;
$config = $oConfigDB->getEsquema('publicv');
$oConexion = new core\dbConnection($config);
$oDevelPC = $oConexion->getPDO();

$oDBRol = new core\DBRol();
$oDBRol->setDbConexion($oDevelPC);

$oDBRol->setUser($esquemav);
$oDBRol->setPwd($esquemav_pwd);
$oDBRol->crearUsuario();
$oConfigDB->addEsquema('sv', $esquemav, $esquemav_pwd);

// sv-e
// Los mismos parametros que para sv.
// Si es el mismo servidor (portatil) me lo salto:
$host_sv = $config['host'];
$port_sv = $config['port'];
$oConfigDB = new core\ConfigDB('importar');
//coge los valores de public: 1.la database sv-e; 2.nombre superusuario; 3.pasword superusuario;
$config = $oConfigDB->getEsquema('publicv-e');
$oConexion = new core\dbConnection($config);
$oDevelPC = $oConexion->getPDO();
$host_sve = $config['host'];
$port_sve = $config['port'];

// Si es el mismo servidor (portatil) me lo salto:
if ($host_sv != $host_sve || $port_sv != $port_sve) {
    $oDBRol = new core\DBRol();
    $oDBRol->setDbConexion($oDevelPC);

    $oDBRol->setUser($esquemav);
    $oDBRol->setPwd($esquemav_pwd);
    $oDBRol->crearUsuario();
}
$oConfigDB->addEsquema('sv-e', $esquemav, $esquemav_pwd);

// sf
/* Si se crea desde sv, hay que crear el Role de sf para la database comun
 * (Garantiza el acceso a actividades y importadas)
 * Si se hace desde sf, además se crea el esquema. (Actualmente en el servidor de sve)
 */

// desde sv y sf:
$oConfigDB = new core\ConfigDB('importar');
$config = $oConfigDB->getEsquema('public'); //de la database comun
$oConexion = new core\dbConnection($config);
$oDevelPC = $oConexion->getPDO();

$oDBRol = new core\DBRol();
$oDBRol->setDbConexion($oDevelPC);

$oDBRol->setUser($esquemaf);
$oDBRol->setPwd($esquemaf_pwd);
$oDBRol->crearUsuario();

// desde sf (añado el esquema al Role)
if ($_SESSION['sfsv'] == 'sf') {
    $oConfigDB = new core\ConfigDB('importar');
    //coge los valores de public: 1.la database sv-e; 2.nombre superusuario; 3.pasword superusuario;
    $config = $oConfigDB->getEsquema('publicv-e');
    $oConexion = new core\dbConnection($config);
    $oDevelPC = $oConexion->getPDO();

    $oDBRol = new core\DBRol();
    $oDBRol->setDbConexion($oDevelPC);

    $oDBRol->setUser($esquemaf);
    $oDBRol->setPwd($esquemaf_pwd);
    $oDBRol->crearUsuario();
    $oConfigDB->addEsquema('sf', $esquemaf, $esquemaf_pwd);
}

$archivo_conf = ConfigGlobal::DIR_PWD . '/  (comun.inc, sv.inc, sf.inc)';
echo sprintf(_("se han creado los usuarios. Ojo, un único usuario para pruebas y producción"));
echo "<br>";
echo sprintf(_("debe copiar los siguientes usuarios y passwords en el archivo %s"), $archivo_conf);
echo "<br>";
echo "<br>";
echo "$esquema > " . htmlspecialchars($esquema_pwd) . "<br>";
echo "$esquemav > " . htmlspecialchars($esquemav_pwd) . "<br>";
echo "$esquemaf > " . htmlspecialchars($esquemaf_pwd) . "<br>";
echo "<br>";
echo _("Ya no hace falta, pero interesa saberlo para acceder al a BD directamente.");





