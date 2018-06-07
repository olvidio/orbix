<?php
namespace permisos\controller;

use permisos\model as permisos;
use usuarios\model\entity as usuarios;
use web;
use core;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos por esta url  **********************************************
/*
$session_config=array (
	'region'=>'H',
	'dele'=>'dlb',
	'gestionActividades'=>2 // 1 => centralizada, 2 => por oficinas.
	 );
$_SESSION['config']=$session_config;
*/
// FIN de  Cabecera global de URL de controlador ********************************

function posibles_esquemas($default='') {
	$txt = '';
	// Lista de posibles esquemas (en comun)
	$oConfig = new core\Config('comun');
	$config = $oConfig->getEsquema('public'); 
	$oConexion = new core\dbConnection($config);
	$oDBP = $oConexion->getPDO();

	$sQuery = "select nspname from pg_namespace where nspowner > 1000 ORDER BY nspname";
	if (($oDblSt = $oDBP->query($sQuery)) === false) {
		$sClauError = 'Schemas.lista';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
		return false;
	}
	if (is_object($oDblSt)) {
		$oDblSt->execute();
		$txt = "<select id=\"esquema\" name=\"esquema\" >";
		foreach($oDblSt as $row) {
			if (!isset($row[1])) { $a = 0; } else { $a = 1; } // para el caso de sólo tener un valor.
			if ($row[0] == 'public') continue;
			if ($row[0] == 'resto') continue;
			$sf = $row[0].'f';
			$sv = $row[0].'v';
			if (!empty($default) && $sf == $default) { $sel_sf = 'selected'; } else { $sel_sf = ''; }
			if (!empty($default) && $sv == $default) { $sel_sv = 'selected'; } else { $sel_sv = ''; }
			$txt .= "<option value=\"$sf\" $sel_sf>$sf</option>";
			$txt .= "<option value=\"$sv\" $sel_sv>$sv</option>";
		}
		$txt .= '</select>';
	}
	return $txt;
}
function cambiar_idioma() {
	// Si no está determinado en las preferencias, miro el del navegador
	if (empty($_SESSION['session_auth']['idioma'])) { 
	// mirar el idioma del navegador
		if (!empty($_SERVER["HTTP_ACCEPT_LANGUAGE"])){ # Verificamos que el visitante haya designado algún idioma
			$a_idiomas = explode(",",$_SERVER["HTTP_ACCEPT_LANGUAGE"]); # Convertimos HTTP_ACCEPT_LANGUAGE en array
			/* Recorremos el array hasta que encontramos un idioma del visitante que coincida con los idiomas
			en que está disponible nuestra web */
			for ($i=0; $i<count($a_idiomas); $i++){
				if (!isset($idioma)){
					if (substr($a_idiomas[$i], 0, 2) == "ca"){$idioma = "ca_ES.UTF-8";}
					if (substr($a_idiomas[$i], 0, 2) == "es"){$idioma = "es_ES.UTF-8";}
					//if (substr($a_idiomas[$i], 0, 2) == "en"){$idioma = "en";}
					//if (substr($a_idiomas[$i], 0, 2) == "fr"){$idioma = "fr";}
				}
			}
		}
	} else {
		$idioma = $_SESSION['session_auth']['idioma'];
	}
	# Si no hemos encontrado ningún idioma que nos convenga, mostramos la web en el idioma por defecto
	if (!isset($idioma)){$idioma = core\ConfigGlobal::$x_default_idioma;}  

	$domain="delegacion";
	bindtextdomain($domain,core\ConfigGlobal::$dir_idiomas);
	textdomain ($domain);
	bind_textdomain_codeset($domain,'UTF-8');
	putenv("LC_ALL=$idioma");
	setlocale(LC_ALL,$idioma);
}

// APLICACIONES POSIBLES
function getAppsPosibles () {
	$oConfig = new core\Config('comun');
	$config = $oConfig->getEsquema('public'); 
	$oConexion = new core\dbConnection($config);
	$oDBP = $oConexion->getPDO();
	$sQuery = "SELECT * FROM m0_apps";
	$a_apps=array();
	foreach ($oDBP->query($sQuery) as $aDades) {
		$nom=$aDades['nom'];
		$a_apps[$nom]=$aDades['id_app'];
	}
	return $a_apps;
}

// MODULOS POSIBLES
function getModsPosibles () {
	$oConfig = new core\Config('comun');
	$config = $oConfig->getEsquema('public'); 
	$oConexion = new core\dbConnection($config);
	$oDBP = $oConexion->getPDO();
	$sQuery = "SELECT * FROM m0_modulos";
	$a_mods=array();
	$a_mods_req=array();
	$a_apps_req=array();
	foreach ($oDBP->query($sQuery) as $aDades) {
		$id_mod=$aDades['id_mod'];
		$nom=$aDades['nom'];
		$mods_req=$aDades['mods_req'];
		$apps_req=$aDades['apps_req'];
		$a_mods[$id_mod] = array('nom' => $nom, 'mods_req' => $mods_req, 'apps_req' => $apps_req);
	}
	return $a_mods;
}

// APLICACIONES INSTALADAS EN LA DL
function getModsInstalados ($oDB) {
	$sQuery = "SELECT * FROM m0_mods_installed_dl WHERE status = 't'";
	$a_mods_installed=array();
	foreach ($oDB->query($sQuery) as $aDades) {
		$id_mod=$aDades['id_mod'];
		$a_mods_installed[$id_mod]=$aDades['param'];
	}
	return $a_mods_installed;
}

function getAppsMods($id_mod) {
	$apps = array();
	$a_mods = getModsPosibles();
	$ajson = $a_mods[$id_mod]['mods_req'];
	if (preg_match('/^{(.*)}$/', $ajson, $matches)) {
		$mod_in = str_getcsv($matches[1]);
		foreach ($mod_in as $mod) {
			$appsi = getApps($mod);
			$apps = array_merge($apps,$appsi);
		}
	}
	return $apps;
}

function getApps($id_mod) {
	$apps = array();
	$a_mods = getModsPosibles();
	$ajson = $a_mods[$id_mod]['apps_req'];
	if (preg_match('/^{(.*)}$/', $ajson, $matches)) {
		$app_in = str_getcsv($matches[1]);

		foreach ($app_in as $app) {
			array_push($apps,$app);
		}
	}
	return $apps;
}


// ara a global_obj. $GLOBALS['oPerm'] = new permisos\PermDl();
//$GLOBALS['oPermActiv'] = new PermActiv;

if ( !isset($_SESSION['session_auth'])) { 
	//el segon cop tinc el nom i el password
	if (isset($_POST['username']) && isset($_POST['password'])) {
		switch(core\ConfigGlobal::$auth_method) {
			case "ldap":
				break;
			case "database":
				$mail='';

				$aWhere = array('usuario'=>$_POST['username']);
				$esquema = $_POST['esquema'];
				if (substr($esquema,-1)=='v') {
					$sfsv = 1;
					$oConfig = new core\Config('sv'); 
					$config = $oConfig->getEsquema($esquema); 
					$oConexion = new core\dbConnection($config);
					$oDB = $oConexion->getPDO();

				}
				if (substr($esquema,-1)=='f') {
					$sfsv = 2;
					$oConfig = new core\Config('sf'); 
					$config = $oConfig->getEsquema($esquema); 
					$oConexion = new core\dbConnection($config);
					$oDB = $oConexion->getPDO();
				}
				$query="SELECT * FROM aux_usuarios WHERE usuario = :usuario";
				if (($oDBSt= $oDB->prepare($query)) === false) {
					$sClauError = 'login_obj.prepare';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
					return false;
				}

				if (($oDBSt->execute($aWhere)) === false) {
					$sClauError = 'loguin_obj.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
					return false;
				}

				$sPasswd = null;
				$oCrypt = new permisos\MyCrypt();
				$oDBSt->bindColumn('password', $sPasswd, \PDO::PARAM_STR);
				if ($row=$oDBSt->fetch(\PDO::FETCH_ASSOC)) {
					if ($oCrypt->encode($_POST['password'],$sPasswd) == $sPasswd) {
						$id_usuario = $row['id_usuario'];
						$id_role = $row['id_role'];
						$oConfig = new core\Config('comun');
						$config = $oConfig->getEsquema('public'); 
						$oConexion = new core\dbConnection($config);
						$oDBP = $oConexion->getPDO();
						$queryr="SELECT * FROM aux_roles WHERE id_role = $id_role";
						if (($oDBPSt= $oDBP->query($queryr)) === false) {
							$sClauError = 'login_obj.prepare';
							$_SESSION['oGestorErrores']->addErrorAppLastError($oDBP, $sClauError, __LINE__, __FILE__);
							return false;
						}
						$row2=$oDBPSt->fetch(\PDO::FETCH_ASSOC);
						$role_pau = $row2['pau'];
						/*
						//Para la oficina, de momento cojo la primera
						$GesGMR = new menus\GestorGrupMenuRole();
						$cGMR = $GesGMR->getGrupMenuRoles(array('id_role'=>$id_role));
						$mi_oficina_menu=$cGMR[0];
						*/
								$perms_activ='';
								$mi_oficina = '';
								$mi_oficina_menu = '';

						// si no tiene mail interior, cojo el exterior.
						$mail = empty($mail)? $row['email'] : $mail;
						$expire=""; //de moment, per fer servir més endevant...
						// Para obligar a cambiar el password
						if ($_POST['password'] == '1ªVegada') {
							$expire=1;
						}
						
						
						$a_apps = getAppsPosibles();
						$app_installed = array();

						$a_mods_installed = getModsInstalados($oDB);
						foreach ($a_mods_installed as $id_mod=>$param) {
							$ap1 = getAppsMods($id_mod);
							$ap2 = getApps($id_mod);
							$app_installed = array_merge($app_installed,$ap1,$ap2);
							$app_installed = array_unique($app_installed);
						}
			
						// Idioma
						$idioma='';
						$query_idioma = sprintf( "select * from web_preferencias where id_usuario = '%s' and tipo = '%s' ",$id_usuario,"idioma");
						$oDBStI=$oDB->query($query_idioma);
						$row = $oDBStI->fetch(\PDO::FETCH_ASSOC);
						$idioma = $row['preferencia'];

						//si existe, registro la sesion con los permisos
						if ( !isset($_SESSION['session_auth'])) { 
							$session_auth=array (
								'id_usuario'=>$id_usuario,
								'sfsv'=>$sfsv,
								'id_role'=>$id_role,
								'role_pau'=>$role_pau,
								'username'=>$_POST['username'],
								'password'=>$_POST['password'],
								'esquema'=>$_POST['esquema'],
								'perms_activ'=>$perms_activ,
								'mi_oficina'=>$mi_oficina,
								'mi_oficina_menu'=>$mi_oficina_menu,
								'expire'=>$expire,
								'mail'=>$mail,
								'idioma'=>$idioma
								 );
							$_SESSION['session_auth']=$session_auth;
						}
						//si existe, registro la sesion con la configuración
						if ( !isset($_SESSION['config'])) { 
							$session_config=array (
								'id_role'=>$id_role,
								'role_pau'=>$role_pau,
								'username'=>$_POST['username'],
								'password'=>$_POST['password'],
								'perms_activ'=>$perms_activ,
								'mi_oficina'=>$mi_oficina,
								'mi_oficina_menu'=>$mi_oficina_menu,
								'expire'=>$expire,
								'mail'=>$mail,
								'idioma'=>$idioma,
								'app_installed'=>$app_installed,
								'a_mods_installed'=>$a_mods_installed,
								'a_apps'=>$a_apps
								 );
							$_SESSION['config']=$session_config;
						}
						/* para la traducción. Después de registrar session_auth */
						cambiar_idioma();
						/* a ver si memoriza el esquema al que entro */
						setcookie("esquema", $esquema, time() + (86400 * 30), "/"); // 86400 = 1 day
					} else {
						$variables = array('error'=>1);
						$variables['DesplRegiones'] = posibles_esquemas($esquema);
						$oView = new core\View(__NAMESPACE__);
						echo $oView->render('login_form.phtml',$variables);
						die();
					}
				} else {
					$variables = array('error'=>1);
					$variables['DesplRegiones'] = posibles_esquemas($esquema);
					$oView = new core\View(__NAMESPACE__);
					echo $oView->render('login_form.phtml',$variables);
					die();
				}
		}
	} else { // el primer cop
		if(!isset($_COOKIE["esquema"])) {
			$esquema = "";
		} else {
			$esquema = $_COOKIE["esquema"];
		}
		$a_campos['DesplRegiones'] = posibles_esquemas($esquema);
		$oView = new core\View(__NAMESPACE__);
		echo $oView->render('login_form.phtml',$a_campos);
		die();
	}
} else {
	// ya esta registrado";
	/**
	 *  parece que los cambios con setlocale son para el proceso,
	 *  no para session ni multithreaded, por tanto hay que hacerlo cada vez
	 *  para la traducción 
	 */
	cambiar_idioma();
}

if ( !isset($_SESSION['session_go_to'])) { 
	$_SESSION['session_go_to']="a";
	// para que la primera vez vaya a la pagina de inicio personalizada:
	$primera=1;		
}