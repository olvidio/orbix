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
$oDBPC = new \PDO(ConfigGlobal::$str_conexio_public);
$oDBPC->exec('SET search_path TO public');
$oDBPC->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
//$oDBPC->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );

$oDBRC = new \PDO(ConfigGlobal::$str_conexio_resto);
$oDBRC->exec('SET search_path TO resto');
$oDBRC->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
//$oDBRC->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
//comun
$oDBC = new \PDO(ConfigGlobal::get_conexio_comu());
$oDBC->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
//$oDBC->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
$user_sfsv =$_SESSION['session_auth']['sfsv']; 
switch ($user_sfsv) {
	case 1: //sv
		$oDB = new \PDO(ConfigGlobal::get_conexio_sv());
		$oDB->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
//		$oDB->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
		$oDBP = new \PDO(ConfigGlobal::$str_conexio_publicv);
		$oDBP->exec('SET search_path TO publicv');
		$oDBP->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
//		$oDBP->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
		$oDBR = new \PDO(ConfigGlobal::$str_conexio_restov);
		$oDBR->exec('SET search_path TO restov');
		$oDBR->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
//		$oDBR->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
		//sf
		$oDBE = new \PDO(ConfigGlobal::get_conexio_sf());
		$oDBEP = new \PDO(ConfigGlobal::$str_conexio_publicf);
		$oDBER = new \PDO(ConfigGlobal::$str_conexio_restof);
		break;
	case 2: //sf
		$oDB = new \PDO(ConfigGlobal::get_conexio_sf());
		$oDB->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
//		$oDB->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
		$oDBP = new \PDO(ConfigGlobal::$str_conexio_publicf);
		$oDBP->exec('SET search_path TO publicf');
		$oDBP->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
//		$oDBP->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
		$oDBR = new \PDO(ConfigGlobal::$str_conexio_restof);
		$oDBR->exec('SET search_path TO restof');
		$oDBR->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
//		$oDBR->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
		//sv
		$oDBE = new \PDO(ConfigGlobal::get_conexio_sv());
		break;
}

// para las tertulias.
//$oDBT = new \PDO(ConfigGlobal::$str_conexio_filmoteca);
//$oDBA->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );


if (configGlobal::is_app_installed('dbextern')) {
	// Para sincronizar con listas Madrid (SQLSERVER)
	if (!isset($_SESSION['oDBListas'])){
		try {
			$oDBListas = new \PDO(ConfigGlobal::$dsn_conexio_listas,ConfigGlobal::$user_conexio_listas,ConfigGlobal::$password_conexio_listas, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING));
		} catch (\PDOException $e) {
			//Hay que poner el mensaje entre /* ... */ para que el script que carga a continuación lo interprete como un comentario.
			echo "/*";
			echo _("No puedo conectar con  la base de datos de listas").':<br>';
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

// func_tablas necesita $oDB
include_once('func_tablas.php'); 

// Para validar los parametros enviados via POST
$oValidator = new web\Hash();
echo $oValidator->validatePost($_POST);

$oPosicion = new web\Posicion($_SERVER['PHP_SELF'],$_POST);
?>
