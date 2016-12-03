<?php
namespace core;
use usuarios\model as usuarios;
use permisos\model as permisos;
use web;

if (session_id() == "") {
	session_start(); // if no active session we start a new one
}

require_once('apps/permisos/controller/login_obj.php');

$_SESSION['oGestorErrores'] = new gestorErrores();

//Definir Conexiones
// public para todo el mundo
$oDBPC = new \PDO(ConfigGlobal::$str_conexio_public);
$oDBPC->exec('SET search_path TO public');
$oDBPC->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");

$oDBRC = new \PDO(ConfigGlobal::$str_conexio_resto);
$oDBRC->exec('SET search_path TO resto');
$oDBRC->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
//comun
$oDBC = new \PDO(ConfigGlobal::get_conexio_comu());
$oDBC->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
$user_sfsv =$_SESSION['session_auth']['sfsv']; 
switch ($user_sfsv) {
	case 1: //sv
		$oDB = new \PDO(ConfigGlobal::get_conexio_sv());
		$oDB->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
		$oDBP = new \PDO(ConfigGlobal::$str_conexio_publicv);
		$oDBP->exec('SET search_path TO publicv');
		$oDBP->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
		$oDBR = new \PDO(ConfigGlobal::$str_conexio_restov);
		$oDBR->exec('SET search_path TO restov');
		$oDBR->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
		//sf
		$oDBE = new \PDO(ConfigGlobal::get_conexio_sf());
		$oDBEP = new \PDO(ConfigGlobal::$str_conexio_publicf);
		$oDBER = new \PDO(ConfigGlobal::$str_conexio_restof);
		break;
	case 2: //sf
		$oDB = new \PDO(ConfigGlobal::get_conexio_sf());
		$oDB->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
		$oDBP = new \PDO(ConfigGlobal::$str_conexio_publicf);
		$oDBP->exec('SET search_path TO publicf');
		$oDBP->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
		$oDBR = new \PDO(ConfigGlobal::$str_conexio_restof);
		$oDBR->exec('SET search_path TO restof');
		$oDBR->exec("SET DATESTYLE TO '".ConfigGlobal::$datestyle."'");
		//sv
		$oDBE = new \PDO(ConfigGlobal::get_conexio_sv());
		break;
}

// para las tertulias.
//$oDBT = new PDO(ConfigGlobal::$str_conexio_filmoteca);
//$oDBA->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );


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
$aPOST = $_POST;
if (isset($aPOST['h'])) {
	$salta = 0;
	$h1 = $aPOST['h'];
	// hash de los campos hidden
	$hh = empty($aPOST['hh'])? '' : $aPOST['hh'];
	// campos hidden. (separados po comas).
	$hhc = empty($aPOST['hhc'])? '' : $aPOST['hhc'];
	// campos del formulario. (separados por '!').
	//$hc = empty($aPOST['hc'])? '' : $aPOST['hc'];
	// campos que se deben quitar del hash. (separados por '!').
	$hno = empty($aPOST['hno'])? '' : $aPOST['hno']; 
	if (!empty($hno)) {
		$a_campos_no = explode('!',$hno);
		foreach ($a_campos_no as $campo) {
			unset($aPOST[$campo]);
		}
	}
	$hchk = empty($aPOST['hchk'])? '' : $aPOST['hchk']; 
	//En el caso de comprobar_campos.php añado tres que hay que borrar
	if ($_SERVER["REQUEST_URI"] == '/orbix/apps/core/comprobar_campos.php') {
			unset($aPOST['cc_tabla']);
			unset($aPOST['cc_obj']);
			unset($aPOST['cc_pau']);
	}
	if (ConfigGlobal::is_debug_mode()) {
		// Url original de la que se ha ehecho el hash. Para comparar con la actual.
		$horig = empty($aPOST['horig'])? '' : $aPOST['horig'];
		$hhorig = empty($aPOST['hhorig'])? '' : $aPOST['hhorig'];
		/*
		$horig = empty($aPOST['horig'])? '' : rawurldecode($aPOST['horig']);
		$hhorig = empty($aPOST['hhorig'])? '' : rawurldecode($aPOST['hhorig']);
		*/
	}
	// Si es un form, paso la lista de campos posibles.
	if (isset($aPOST['hh'])) {
		unset($aPOST['PHPSESSID']);
		unset($aPOST['atras']);
		unset($aPOST['h']);
		//unset($aPOST['hc']);
		unset($aPOST['horig']);
		unset($aPOST['hh']);
		unset($aPOST['hhc']);
		unset($aPOST['hhorig']);
		unset($aPOST['hno']);
		unset($aPOST['hchk']);
		unset($aPOST['hnov']);

		// Que los campos hidden sean los mismos y con los mismos valores.
		//lista de campos hidden
		$a_campos = explode('!',$hhc);
		$aCampos = array();
		foreach($a_campos as $campo) {
			if (empty($campo)) continue;
			// no puedo usar empty por los valores '0'
			if(isset($aPOST[$campo])) {
				//$aCampos[$campo] = rawurldecode($aPOST[$campo]);
				$aCampos[$campo] = $aPOST[$campo];
			} else {
				$aCampos[$campo] = '';
			}
		}
		$h2 = web\Hash::camposArray($aCampos)['hash'];
		$sUrl = web\Hash::camposArray($aCampos)['orig'];

		if ($hh !== $h2) {
			$salta = 1;
			if (ConfigGlobal::is_debug_mode()) {
				$salta_txt = _("LLegan campos hidden modificados");
				$horig = $hhorig;
			}
		} else {
			// Que los campos checkbox sean los mismos sin tener en cuenta los mismos valores.
			//lista de campos chck
			$a_campos = explode('!',$hchk);
			foreach($a_campos as $campo) {
				if (empty($campo)) continue;
				$aPOST[$campo] = '';
			}

			$rta = web\Hash::camposArray($aPOST,'sin_valor');
			$h2 = $rta['hash'];
			$sUrl = $rta['orig'];
			if ($h1 !== $h2) {
				$salta = 1;
				if (ConfigGlobal::is_debug_mode()) {
					$salta_txt = _("LLegan distinto número o nombre de los campos que se dice que se envian");
				}
			}
		}
	} else {
		//si es hnov es 1, es para no tener en cuenta los valores de los parametros en el hash.
		$hnov = empty($aPOST['hnov'])? '' : $aPOST['hnov'];
		unset($aPOST['PHPSESSID']);
		unset($aPOST['atras']);
		unset($aPOST['h']);
		unset($aPOST['horig']);
		unset($aPOST['hh']);
		unset($aPOST['hhc']);
		unset($aPOST['hhorig']);
		unset($aPOST['hno']);
		unset($aPOST['hchk']);
		unset($aPOST['hnov']);
		ksort($aPOST);
		if ($hnov == 1) { // borro posibles los valores de los campos
			foreach($aPOST as $camp=>$valor){
				$aPOST[$camp] = '';
			}
		}
		if (empty($_SERVER["SERVER_PORT"])) {
			$port = '';
		} else {
			$port = ':'.$_SERVER["SERVER_PORT"];
		}

		if (empty($aPOST)) {
			$sUrl = '//'.$_SERVER["SERVER_NAME"].$port.$_SERVER["SCRIPT_NAME"];
		} else {
			$sUrl = '//'.$_SERVER["SERVER_NAME"].$port.$_SERVER["SCRIPT_NAME"].'?'.http_build_query($aPOST);
		}

		//echo "ccc: $sUrl<br>";
		$rta = web\Hash::md($sUrl);
		$h2 = $rta['hash'];
		$h2orig = $rta['orig'];

		if ($h1 !== $h2) {
			$salta = 1;
			$salta_txt = _("Url modificada");
		}
	}
	if ($salta 	== 1) {
		if (ConfigGlobal::is_debug_mode()) {
			echo $salta_txt.'<br>';
			echo "url (h1) emisor  : $horig<br>";
			echo "url (h2) receptor: $sUrl<br>";
			echo "h1: $h1; h2: $h2;  ".var_dump($h1===$h2);
	   } else {
			exit (_("No deberias cruzar el Missisipi..."));
	   }
	}
} else {
	//evito los scripts
	$salta = 0;
	if (strpos($_SERVER["REQUEST_URI"],'/index.php') !== false) { $salta = 1; }
	if (strpos($_SERVER["REQUEST_URI"],'udm4') !== false) { $salta = 1; }
	if ($salta == 0 && (ConfigGlobal::mi_id_role() == 1)) {
		echo "<div>"._("página con seguridad desactivada")."</div>";
	}
}

$oPosicion = new web\Posicion($_SERVER['PHP_SELF'],$_POST);
?>
