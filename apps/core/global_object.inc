<?php
namespace core;
use usuarios\model as usuarios;
use permisos\model as permisos;
use web;

if (session_id() == "") session_start(); // if no active session we start a new one

require_once('apps/permisos/controller/login_obj.php');

$_SESSION['oGestorErrores'] = new gestorErrores();

//Definir Conexiones
// public para todo el muindo
$oDBPC = new \PDO(ConfigGlobal::$str_conexio_public);
$oDBRC = new \PDO(ConfigGlobal::$str_conexio_resto);
//comun
$oDBC = new \PDO(ConfigGlobal::get_conexio_comu());
$user_sfsv =$_SESSION['session_auth']['sfsv']; 
switch ($user_sfsv) {
	case 1: //sv
		$oDB = new \PDO(ConfigGlobal::get_conexio_sv());
		$oDBP = new \PDO(ConfigGlobal::$str_conexio_publicv);
		$oDBR = new \PDO(ConfigGlobal::$str_conexio_restov);
		//sf
		$oDBE = new \PDO(ConfigGlobal::get_conexio_sf());
		$oDBEP = new \PDO(ConfigGlobal::$str_conexio_publicf);
		$oDBER = new \PDO(ConfigGlobal::$str_conexio_restof);
		break;
	case 2: //sf
		$oDB = new \PDO(ConfigGlobal::get_conexio_sf());
		$oDBP = new \PDO(ConfigGlobal::$str_conexio_publicf);
		$oDBR = new \PDO(ConfigGlobal::$str_conexio_restof);
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
	//En el caso de comprobar_campos.php añado tres que hay que borrar
	if ($_SERVER["REQUEST_URI"] == '/orbix/apps/core/comprobar_campos.php') {
			unset($aPOST['cc_tabla']);
			unset($aPOST['cc_obj']);
			unset($aPOST['cc_pau']);
	}
	if (ConfigGlobal::is_debug_mode()) {
		// Url original de la que se ha ehecho el hash. Para comparar con la actual.
		$horig = empty($aPOST['horig'])? '' : urldecode($aPOST['horig']);
		$hhorig = empty($aPOST['hhorig'])? '' : urldecode($aPOST['hhorig']);
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
		unset($aPOST['hnov']);

		// Que los campos hidden sean los mismos y con los mismos valores.

		//lista de campos hidden
		$a_campos = explode('!',$hhc);
		$aCmpos = array();
		foreach($a_campos as $campo) {
			if (empty($campo)) continue;
			//$sQuery .= empty($sQuery)? $campo.'=' : '&'.$campo.'=';
			$aCampos[$campo] = !empty($aPOST[$campo])? $aPOST[$campo] : '';
		}

		$h2 = web\Hash::camposArray($aCampos)['hash'];
		$sUrl = web\Hash::camposArray($aCampos)['orig'];

/*

		$sUrl = '';
		sort($a_campos);
		$aCampos = array();
		foreach ($a_campos as $campo) {
			if (empty($campo)) continue;
			//$sQuery .= empty($sQuery)? $campo.'=' : '&'.$campo.'=';
			$aCampos[$campo] = !empty($aPOST[$campo])? $aPOST[$campo] : '';
		}
		$sUrl .= http_build_query($aCampos);

		foreach ($a_campos as $campo) {
			$sUrl .= empty($sUrl)? $campo.'='.$aPOST[$campo] : '&'.$campo.'='.$aPOST[$campo];
		}

		$rta = web\Hash::md($sUrl);
		$h2 = $rta['hash'];
		$sUrl = $rta['orig'];
		*/
		if ($hh !== $h2) {
			$salta = 1;
			if (ConfigGlobal::is_debug_mode()) {
				$salta_txt = _("LLegan campos hidden modificados");
				$horig = $hhorig;
			}
		} else {
			$rta = web\Hash::camposArray($aPOST,'sin_valor');
			$h2 = $rta['hash'];
			//$sUrl = urldecode($rta['orig']);
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
		//unset($aPOST['hc']);
		unset($aPOST['horig']);
		unset($aPOST['hh']);
		unset($aPOST['hhc']);
		unset($aPOST['hhorig']);
		unset($aPOST['hno']);
		unset($aPOST['hnov']);
		ksort($aPOST);
		if ($hnov == 1) { // borro posibles los valores de los campos
			foreach($aPOST as $camp=>$valor){
				$aPOST[$camp] = '';
			}
		}
		/*
		$query = '';
		foreach($aPOST as $camp=>$valor){
			if ($hnov == 1) $valor='';
			$query .= empty($query)? '?'.$camp.'='.$valor : '&'.$camp.'='.$valor;
		}
		*/
		// NO Puedo usar http_build_query porque codifica y en los originales está sin codificar.
		//$sUrl = urlencode('//'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"].'?').http_build_query($aPOST);
		//$sUrl = '//'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"].$query;
		//$sUrl = web\Hash::ordenarParam($sUrl);
		if (empty($aPOST)) {
			$sUrl = '//'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		} else {
			$sUrl = '//'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"].'?'.http_build_query($aPOST);
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
