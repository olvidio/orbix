<?php
namespace core;

use usuarios\model\entity as usuarios;
use permisos\model as permisos;
use web;

if (session_id() == "") {
	session_start(); // if no active session we start a new one
}

require_once('apps/permisos/controller/login_obj.php');

$_SESSION['oGestorErrores'] = new gestorErrores();

/*
 * Definir Conexiones
 *
 * No conviene que las conexiones sean persistentes, pues en caso de algún bloqueo
 *  o error afectan a toda la aplicación.
 * De esta manera (no persistente) se cierra la conexión cada vez que acaba el script.
 * 
 */

// public para todo el mundo
$oConfig = new Config('comun'); //de la database comun 

$config = $oConfig->getEsquema('public'); 
$oConexion = new dbConnection($config);
$oDBPC = $oConexion->getPDO();

$config = $oConfig->getEsquema('resto'); 
$oConexion = new dbConnection($config);
$oDBRC = $oConexion->getPDO();

$user_sfsv =$_SESSION['session_auth']['sfsv']; 
switch ($user_sfsv) {
	case 1: //sv
		$esquemav = $_SESSION['session_auth']['esquema'];
		$esquema = \substr($esquemav, 0, -1);
		$esquemaf = $esquema.'f';
		//comun
		$oConfig = new Config('comun'); 
		$config = $oConfig->getEsquema($esquema); 
		$oConexion = new dbConnection($config);
		$oDBC = $oConexion->getPDO();
		//sv
		$oConfig = new Config('sv'); 
		$config = $oConfig->getEsquema($esquemav); 
		$oConexion = new dbConnection($config);
		$oDB = $oConexion->getPDO();

		$config = $oConfig->getEsquema('publicv'); 
		$oConexion = new dbConnection($config);
		$oDBP = $oConexion->getPDO();

		$config = $oConfig->getEsquema('restov'); 
		$oConexion = new dbConnection($config);
		$oDBR = $oConexion->getPDO();
		
		//sf
		$oConfig = new Config('sf'); 
		$config = $oConfig->getEsquema($esquemaf); 
		$oConexion = new dbConnection($config);
		$oDBE = $oConexion->getPDO();

		$config = $oConfig->getEsquema('publicf'); 
		$oConexion = new dbConnection($config);
		$oDBEP = $oConexion->getPDO();

		$config = $oConfig->getEsquema('restof'); 
		$oConexion = new dbConnection($config);
		$oDBER = $oConexion->getPDO();
		
		break;
	case 2: //sf
		$esquemaf = $_SESSION['session_auth']['esquema'];
		$esquema = \substr($esquema, 0, -1);
		$esquemav = $esquema.'v';
		//comun
		$oConfig = new Config('comun'); 
		$config = $oConfig->getEsquema($esquema); 
		$oConexion = new dbConnection($config);
		$oDBC = $oConexion->getPDO();
		//sf
		$oConfig = new Config('sf'); 
		$config = $oConfig->getEsquema($esquemaf); 
		$oConexion = new dbConnection($config);
		$oDB = $oConexion->getPDO();

		$config = $oConfig->getEsquema('publicf'); 
		$oConexion = new dbConnection($config);
		$oDBP = $oConexion->getPDO();

		$config = $oConfig->getEsquema('restof'); 
		$oConexion = new dbConnection($config);
		$oDBR = $oConexion->getPDO();
		
		//sv
		$oConfig = new Config('sv'); 
		$config = $oConfig->getEsquema($esquemav); 
		$oConexion = new dbConnection($config);
		$oDBE = $oConexion->getPDO();
		break;
}

if (configGlobal::is_app_installed('dbextern')) {
	// Para sincronizar con listas Madrid (SQLSERVER)
	// No en el caso de cr (H-Hv)
	if ((\core\ConfigGlobal::mi_region() != \core\ConfigGlobal::mi_dele()) && !isset($_SESSION['oDBListas'])){
		try {
			$oConfig = new Config('listas'); 
			$config = $oConfig->getEsquema('public'); 
			$oConexion = new dbConnection($config);
			$oDBListas = $oConexion->getPDOListas();
		} catch (\PDOException $e) {
			//Hay que poner el mensaje entre /* ... */ para que el script que carga a continuación lo interprete como un comentario.
			echo "/*";
			echo _("No puedo conectar con la base de datos de listas").':<br>';
			echo $e->getMessage();
			echo "*/";
			$_SESSION['oDBListas'] = 'error';
		}
	}
}


if ( ConfigGlobal::is_app_installed('menus') ) {
	if (empty($_SESSION['iPermMenus'])) { // con hacerlo una vez basta.
		// Grupos
		$oGesGrupo = new usuarios\GestorUsuarioGrupo();
		$cGrupos = $oGesGrupo->getUsuariosGrupos(array('id_usuario'=>ConfigGlobal::mi_id_usuario()));
		$iperm_menu = 0;
		foreach ($cGrupos as $UsuarioGrupo) {
			$id_grupo = $UsuarioGrupo->getId_grupo();
			$oGesPermMenu = new usuarios\GestorPermMenu();
			$cPermMenu = $oGesPermMenu->getPermMenus(array('id_usuario'=>$id_grupo));
			foreach ($cPermMenu as $oPermMenu) {
				// Or (inclusive or) 	Bits that are set in either $a or $b are set.
				$iperm_menu = $iperm_menu | $oPermMenu->getMenu_perm();
			}
		}
		//echo "perms: $iperm_menu<br>";
		$_SESSION['iPermMenus'] = $iperm_menu;
		$_SESSION['oPerm'] = new permisos\PermDl();
		$_SESSION['oPerm']->setAccion($iperm_menu);
	}
}


if ( ConfigGlobal::is_app_installed('procesos') ) {
	// para mantener los permisos por actividades en una variable
	if (empty($_SESSION['oPermActividades'])) {
		$_SESSION['oPermActividades'] = new permisos\PermisosActividades(ConfigGlobal::mi_id_usuario());
	}
	$oPermActividades = $_SESSION['oPermActividades'];
} else {
	// para mantener los permisos por actividades en una variable
	if (empty($_SESSION['oPermActividades'])) {
		$_SESSION['oPermActividades'] = new permisos\PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
	}
	$oPermActividades = $_SESSION['oPermActividades'];
}

// func_tablas necesita $oDB (Ya no!)
include_once('func_tablas.php'); 

// Para validar los parametros enviados via POST
$oValidator = new web\Hash();
echo $oValidator->validatePost($_POST);

$oPosicion = new web\Posicion($_SERVER['PHP_SELF'],$_POST);