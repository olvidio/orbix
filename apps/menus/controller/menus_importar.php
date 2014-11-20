<?php
use menus\model as menus;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// Copiar de dlb a public roles-grupmenu, grupmenu, menus
$oDB = $GLOBALS['oDB'];
$oDBPC = $GLOBALS['oDBPC'];

if (core\ConfigGlobal::mi_dele() == 'dlb') {
	echo _("casi seguro que no quieres hacerlo.");
	echo "<br>";
}

$seguro = empty($_POST['seguro'])? 2 : $_POST['seguro'];

if ($seguro == 2) {
	$go=web\Hash::link('apps/menus/controller/menus_importar.php?'.http_build_query(array('seguro'=>1)));
	$html = "Esto pondrá los menus por defecto. Se eliminaran todas las modificaciones que se hayan hecho en los menus y grupos de menu";
	$html .= "<br>";
	$html .= "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go');\">". _("continuar")."</span>";
	echo $html;
}

if ($seguro == 1) {
	//************ GRUPMENU **************
	$sql_del = 'TRUNCATE TABLE aux_grupmenu RESTART IDENTITY CASCADE';
	if ($qRs = $oDB->query($sql_del) === false) {
		$sClauError = 'ExportarMenu.VaciarTabla';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
		return false;
	}

	$sQry = 'SELECT * FROM ref_grupmenu';
	foreach ( $oDBPC->query($sQry,PDO::FETCH_ASSOC) as $aDades) { 
		//print_r($aDades);
		$campos="(id_grupmenu,grup_menu,orden)";
		$valores="(:id_grupmenu,:grup_menu,:orden)";
		if (($qRs = $oDB->prepare("INSERT INTO aux_grupmenu $campos VALUES $valores")) === false) {
			$sClauError = 'Importar.insertar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
			return false;
		} else {
			if ($qRs->execute($aDades) === false) {
				$sClauError = 'Importar.insertar.execute';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
				return false;
			}
		}
	}
	//************ GRUPMENU_ROL**************
	$sql_del = 'TRUNCATE TABLE aux_grupmenu_rol RESTART IDENTITY CASCADE';
	if ($qRs = $oDB->query($sql_del) === false) {
		$sClauError = 'ExportarMenu.VaciarTabla';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
		return false;
	}

	$sQry = 'SELECT * FROM ref_grupmenu_rol';
	foreach ( $oDBPC->query($sQry,PDO::FETCH_ASSOC) as $aDades) { 
		//print_r($aDades);
		$campos="(id_item,id_grupmenu,id_role)";
		$valores="(:id_item,:id_grupmenu,:id_role)";
		if (($qRs = $oDB->prepare("INSERT INTO aux_grupmenu_rol $campos VALUES $valores")) === false) {
			$sClauError = 'Importar.insertar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
			return false;
		} else {
			if ($qRs->execute($aDades) === false) {
				$sClauError = 'Importar.insertar.execute';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
				return false;
			}
		}
	}
	//************ MENUS**************
	$sql_del = 'TRUNCATE TABLE aux_menus RESTART IDENTITY CASCADE';
	if ($qRs = $oDB->query($sql_del) === false) {
		$sClauError = 'ExportarMenu.VaciarTabla';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
		return false;
	}

	$sQry = 'SELECT * FROM ref_menus';
	foreach ( $oDBPC->query($sQry,PDO::FETCH_ASSOC) as $aDades) { 
		//print_r($aDades);
		$campos="(id_menu,orden,menu,parametros,id_metamenu,menu_perm,id_grupmenu,ok)";
		$valores="(:id_menu,:orden,:menu,:parametros,:id_metamenu,:menu_perm,:id_grupmenu,:ok)";
		if (($qRs = $oDB->prepare("INSERT INTO aux_menus $campos VALUES $valores")) === false) {
			$sClauError = 'Importar.insertar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
			return false;
		} else {
			if ($qRs->execute($aDades) === false) {
				$sClauError = 'Importar.insertar.execute';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
				return false;
			}
		}
	}
}
?>
