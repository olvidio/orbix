<?php
namespace core;

use usuarios\model\entity as usuarios;
use permisos\model as permisos;
use web;
use config\model\Config;

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
$oConfigDB = new ConfigDB('comun'); //de la database comun 

$config = $oConfigDB->getEsquema('public'); 
$oConexion = new dbConnection($config);
$oDBPC = $oConexion->getPDO();

$config = $oConfigDB->getEsquema('resto'); 
$oConexion = new dbConnection($config);
$oDBRC = $oConexion->getPDO();

$user_sfsv =$_SESSION['session_auth']['sfsv']; 
switch ($user_sfsv) {
	case 1: //sv
		$esquemav = $_SESSION['session_auth']['esquema'];
		$esquema = \substr($esquemav, 0, -1);
		$esquemaf = $esquema.'f';
		//comun
		$oConfigDB->setDataBase('comun'); 
		$config = $oConfigDB->getEsquema($esquema); 
		$oConexion = new dbConnection($config);
		$oDBC = $oConexion->getPDO();
		//sv
		if (ConfigGlobal::is_dmz()) {
		    /*
            $oConfigDB->setDataBase('comun'); 
            $config = $oConfigDB->getEsquema($esquemav); 
            $oConexion = new dbConnection($config);
            $oDB = $oConexion->getPDO();
            
            $config = $oConfigDB->getEsquema('publicv'); 
            $oConexion = new dbConnection($config);
            $oDBP = $oConexion->getPDO();

            $config = $oConfigDB->getEsquema('restov'); 
            $oConexion = new dbConnection($config);
            $oDBR = $oConexion->getPDO();
            */
		} else {
            $oConfigDB->setDataBase('sv'); 
            $config = $oConfigDB->getEsquema($esquemav); 
            $oConexion = new dbConnection($config);
            $oDB = $oConexion->getPDO();

            $config = $oConfigDB->getEsquema('publicv'); 
            $oConexion = new dbConnection($config);
            $oDBP = $oConexion->getPDO();

            $config = $oConfigDB->getEsquema('restov'); 
            $oConexion = new dbConnection($config);
            $oDBR = $oConexion->getPDO();
		} 
		//sv exterior
		$oConfigDB->setDataBase('sv-e'); 
		$config = $oConfigDB->getEsquema($esquemav); 
		$oConexion = new dbConnection($config);
		$oDBE = $oConexion->getPDO();

		$config = $oConfigDB->getEsquema('publicv'); 
		$oConexion = new dbConnection($config);
		$oDBEP = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('restov'); 
        $oConexion = new dbConnection($config);
        $oDBER = $oConexion->getPDO();
		break;
	case 2: //sf
		$esquemaf = $_SESSION['session_auth']['esquema'];
		$esquema = \substr($esquemaf, 0, -1);
		$esquemav = $esquema.'v';
		//comun
		$oConfigDB->setDataBase('comun'); 
		$config = $oConfigDB->getEsquema($esquema); 
		$oConexion = new dbConnection($config);
		$oDBC = $oConexion->getPDO();

		//sf
		$oConfigDB->setDataBase('sf'); 
		$config = $oConfigDB->getEsquema($esquemaf); 
		$oConexion = new dbConnection($config);
		$oDB = $oConexion->getPDO();
		$oDBE = $oConexion->getPDO();

		$config = $oConfigDB->getEsquema('publicf'); 
		$oConexion = new dbConnection($config);
		$oDBP = $oConexion->getPDO();
		$oDBEP = $oConexion->getPDO();

		$config = $oConfigDB->getEsquema('restof'); 
		$oConexion = new dbConnection($config);
		$oDBR = $oConexion->getPDO();
		
		//sf exterior
		// De momento todo junto. No distingo tunel/dmz
		/*
		$oConfigDB->setDataBase('sf-e'); 
		$config = $oConfigDB->getEsquema($esquemaf); 
		$oConexion = new dbConnection($config);
		$oDBE = $oConexion->getPDO();

		$config = $oConfigDB->getEsquema('publicf'); 
		$oConexion = new dbConnection($config);
		$oDBEP = $oConexion->getPDO();
        */
		break;
}

if (configGlobal::is_app_installed('dbextern') && !ConfigGlobal::is_dmz()) {
	// Para sincronizar con listas Madrid (SQLSERVER)
	// No en el caso de cr (H-Hv)
	if ((\core\ConfigGlobal::mi_region() != \core\ConfigGlobal::mi_delef()) && !isset($_SESSION['oDBListas'])){
		try {
			$oConfigDB = new ConfigDB('listas'); 
			$config = $oConfigDB->getEsquema('public'); 
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

// En el caso cr-stgr, refrescar las vistas
if ((\core\ConfigGlobal::mi_region() === \core\ConfigGlobal::mi_delef()) && !isset($_SESSION['Refresh'])){
	try {
		$views = [
				'd_profesor_latin',
				'd_profesor_ampliacion',
				'd_profesor_director',
				'd_profesor_juramento',
				'd_profesor_stgr',
				'd_publicaciones',
				'd_congresos',
				'd_docencia_stgr',
				'd_titulo_est',
				'p_agregados',
				'p_numerarios',
				'personas_dl',
				'd_teleco_personas_dl',
			];
	    /*
		$views = [
				'p_agregados',
			];
			*/
		
        if ($user_sfsv == 1) {
		    $schema = $esquemav;
		} elseif ($user_sfsv == 2) {
		    $schema = $esquemaf;
		}

		$oMatView = new DBView($schema);
		foreach ($views as $view) {
		    $oMatView->setView($view);
		    if ($oMatView->Existe()) {
		        //true
		        $oMatView->Refresh();
		    } else {
		        $oMatView->create();
		    }
		}
		$_SESSION['Refresh'] = 'ok';
	} catch (\PDOException $e) {
		//Hay que poner el mensaje entre /* ... */ para que el script que carga a continuación lo interprete como un comentario.
		echo "/*";
		echo _("No puedo refrescar las vistas").':<br>';
		echo $e->getMessage();
		echo "*/";
		$_SESSION['Refresh'] = 'error';
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

// Datos de configuración propios de cada dl.
$_SESSION['oConfig'] = new Config();

// para mantener los permisos por actividades en una variable
if (empty($_SESSION['oPermActividades'])) {
    if ( ConfigGlobal::is_app_installed('procesos') ) {
		//$_SESSION['oPermActividades'] = new permisos\PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
		$_SESSION['oPermActividades'] = new permisos\PermisosActividades(ConfigGlobal::mi_id_usuario());
    } else {
		$_SESSION['oPermActividades'] = new permisos\PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
	}
	//$oPermActividades = $_SESSION['oPermActividades'];
}

// func_tablas necesita $oDB (Ya no!)
include_once('func_tablas.php'); 

// Para validar los parametros enviados via POST
$oValidator = new web\Hash();
echo $oValidator->validatePost($_POST);

$oPosicion = new web\Posicion($_SERVER['PHP_SELF'],$_POST);