<?php
namespace permisos\controller;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBPropiedades;
use core\View;
use core\dbConnection;
use permisos\model\MyCrypt;
                
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos por esta url  **********************************************
// 
// FIN de  Cabecera global de URL de controlador ********************************

function cambiar_idioma($idioma='') {
	if (empty($idioma)) {
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
						if (substr($a_idiomas[$i], 0, 2) == "en"){$idioma = "en_US.UTF-8";}
						if (substr($a_idiomas[$i], 0, 2) == "de"){$idioma = "de_DE.UTF-8";}
						//if (substr($a_idiomas[$i], 0, 2) == "en"){$idioma = "en";}
						//if (substr($a_idiomas[$i], 0, 2) == "fr"){$idioma = "fr";}
					}
				}
			}
		} else {
			$idioma = $_SESSION['session_auth']['idioma'];
		}
		# Si no hemos encontrado ningún idioma que nos convenga, mostramos la web en el idioma por defecto
		if (!isset($idioma)){$idioma = $_SESSION['oConfig']->getIdioma_default();}  
	}
	//$idioma=  str_replace('UTF-8', 'utf8', $idioma);
	$domain="orbix";
//	echo "dir: ".core\ConfigGlobal::$dir_languages."<br>";
//	echo "domain: $domain, id: $idioma<br>";
	setlocale(LC_MESSAGES, "");
	putenv("LC_ALL=''");
	putenv("LANGUAGE=");
	
	setlocale(LC_MESSAGES,$idioma);
	putenv("LC_ALL={$idioma}");
	putenv("LANG={$idioma}");
	
	bindtextdomain($domain,ConfigGlobal::$dir_languages);
	textdomain ($domain);
	bind_textdomain_codeset($domain,'UTF-8');
}

// APLICACIONES POSIBLES
function getAppsPosibles () {
	$oConfigDB = new ConfigDB('comun');
	$config = $oConfigDB->getEsquema('public'); 
	$oConexion = new dbConnection($config);
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
	$oConfigDB = new ConfigDB('comun');
	$config = $oConfigDB->getEsquema('public'); 
	$oConexion = new dbConnection($config);
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
	$a_mods = getModsPosibles();
	$sQuery = "SELECT * FROM m0_mods_installed_dl WHERE status = 't'";
	$a_mods_installed=array();
	foreach ($oDB->query($sQuery) as $aDades) {
		$id_mod=$aDades['id_mod'];
        $nom_mod = $a_mods[$id_mod]['nom'];
		$a_mods_installed[$id_mod]=$nom_mod;
	}
	return $a_mods_installed;
}

function getAppsMods($id_mod) {
	$apps = array();
	$a_mods = getModsPosibles();
	$ajson = $a_mods[$id_mod]['mods_req'];
	if (preg_match('/^{(.*)}$/', $ajson, $matches)) {
	    if (!empty($matches[1])) {
            $mod_in = str_getcsv($matches[1]);
            foreach ($mod_in as $mod) {
                $appsi = getApps($mod);
                $apps = array_merge($apps,$appsi);
            }
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

function logout($ubicacion,$idioma,$esquema,$error,$esquema_web='') {
    $oDBPropiedades = new DBPropiedades();
    $a_campos = [];
    $a_campos['error'] = $error;
    $a_campos['ubicacion'] = $ubicacion;
    $a_campos['esquema_web'] = $esquema_web;
    $a_campos['DesplRegiones'] = $oDBPropiedades->posibles_esquemas($esquema);
    $a_campos['idioma'] = $idioma;
    $a_campos['url'] = ConfigGlobal::getWeb();
    $oView = new View(__NAMESPACE__);
    echo $oView->render('login_form2.phtml',$a_campos);
}

// ara a global_obj. $GLOBALS['oPerm'] = new permisos\PermDl();
//$GLOBALS['oPermActiv'] = new PermActiv;
$esquema_web = getenv('ESQUEMA');
$ubicacion = getenv('UBICACION');
$_SESSION['sfsv'] = $ubicacion;

if (!empty($esquema_web)) {
    $oDBPropiedades = new DBPropiedades();
    $a_posibles_esquemas = $oDBPropiedades->array_posibles_esquemas();
    if (!in_array($esquema_web, $a_posibles_esquemas)) {
        $msg = sprintf(_("No existe este equema: %s"),$esquema_web);
        die ($msg);
    }
}

if ( !isset($_SESSION['session_auth'])) { 
	//el segon cop tinc el nom i el password
    $idioma='';
	if (isset($_POST['username']) && isset($_POST['password'])) {
		switch(ConfigGlobal::$auth_method) {
			case "ldap":
				break;
			case "database":
				$mail='';

				$aWhere = array('usuario'=>$_POST['username']);
				$esquema = $_POST['esquema'];
				if (substr($esquema,-1)=='v') {
					$sfsv = 1;
					$oConfigDB = new ConfigDB('sv-e'); 
					$config = $oConfigDB->getEsquema($esquema); 
					$oConexion = new dbConnection($config);
					$oDB = $oConexion->getPDO();

				}
				if (substr($esquema,-1)=='f') {
					$sfsv = 2;
					$oConfigDB = new ConfigDB('sf-e'); 
					$config = $oConfigDB->getEsquema($esquema); 
					$oConexion = new dbConnection($config);
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

                $idioma='';
				$sPasswd = null;
				$oCrypt = new MyCrypt();
				$oDBSt->bindColumn('password', $sPasswd, \PDO::PARAM_STR);
				if ($row=$oDBSt->fetch(\PDO::FETCH_ASSOC)) {
					if ($oCrypt->encode($_POST['password'],$sPasswd) == $sPasswd) {
						$id_usuario = $row['id_usuario'];
						$id_role = $row['id_role'];
						$oConfigDB = new ConfigDB('comun');
						$config = $oConfigDB->getEsquema('public'); 
						$oConexion = new dbConnection($config);
						$oDBP = $oConexion->getPDO();
						$queryr="SELECT * FROM aux_roles WHERE id_role = $id_role";
						if (($oDBPSt= $oDBP->query($queryr)) === false) {
							$sClauError = 'login_obj.prepare';
							$_SESSION['oGestorErrores']->addErrorAppLastError($oDBP, $sClauError, __LINE__, __FILE__);
							return false;
						}
						$row2=$oDBPSt->fetch(\PDO::FETCH_ASSOC);
						$role_pau = $row2['pau'];
						
						// Para la MDZ, solo roles DMZ
						if (ConfigGlobal::is_dmz()) {
						    $role_dmz = $row2['dmz'];
						    if (empty($role_dmz)) {
                                $error = 2;
                                logout($ubicacion,$idioma,$esquema,$error,$esquema_web);
                                die();
						    }
						}
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
						
						
						$a_mods = getModsPosibles();
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
								'mod_installed'=>$a_mods_installed,
								'a_apps'=>$a_apps,
								'a_mods'=>$a_mods,
								 );
							$_SESSION['config']=$session_config;
						}
						/* para la traducción. Después de registrar session_auth */
						cambiar_idioma();
						/* a ver si memoriza el esquema al que entro */
						setcookie("esquema", $esquema, time() + (86400 * 30), "/"); // 86400 = 1 day
						setcookie("idioma", $idioma, time() + (86400 * 30), "/"); // 86400 = 1 day
						/* Hacer que vaya a la pagina de inicio.
						 * No funciona, */
						//header("Location: ".ConfigGlobal::getWeb(), true, 301);
					} else {
					    $error = 1;
                        logout($ubicacion,$idioma,$esquema,$error,$esquema_web);
						die();
					}
				} else {
                    $error = 1;
                    logout($ubicacion,$idioma,$esquema,$error,$esquema_web);
					die();
				}
		}
	} else { // el primer cop
		$esquema = (!isset($_COOKIE["esquema"]))? "" : $_COOKIE["esquema"];
		$idioma = (!isset($_COOKIE["idioma"]))? "" : $_COOKIE["idioma"];
		cambiar_idioma($idioma);	
        $error = 0;
        logout($ubicacion,$idioma,$esquema,$error,$esquema_web);
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

if ( !isset($_SESSION['session_go_to'])) { 
	$_SESSION['session_go_to']="a";
	// para que la primera vez vaya a la pagina de inicio personalizada:
	$primera=1;		
}