<?php
use menus\model as menus;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once('apps/web/func_web.php');

// Copiar de dlb a public roles-grupmenu, grupmenu, menus
$oDevel = $GLOBALS['oDB'];
$oDevelPC = $GLOBALS['oDBPC'];

//************ GRUPMENU **************
$sql_del = 'TRUNCATE TABLE ref_grupmenu RESTART IDENTITY';
if ($qRs = $oDevelPC->query($sql_del) === false) {
	$sClauError = 'ExportarMenu.VaciarTabla';
	$_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPC, $sClauError, __LINE__, __FILE__);
	return false;
}

$sQry = 'SELECT * FROM aux_grupmenu';
foreach ( $oDevel->query($sQry,PDO::FETCH_ASSOC) as $aDades) { 
	unset($aDades['id_schema']);
	//print_r($aDades);
	$campos="(id_grupmenu,grup_menu,orden)";
	$valores="(:id_grupmenu,:grup_menu,:orden)";
	if (($qRs = $oDevelPC->prepare("INSERT INTO ref_grupmenu $campos VALUES $valores")) === false) {
		$sClauError = 'Exposrtar.insertar.prepare';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPC, $sClauError, __LINE__, __FILE__);
		return false;
	} else {
		if ($qRs->execute($aDades) === false) {
			$sClauError = 'Exposrtar.insertar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPC, $sClauError, __LINE__, __FILE__);
			return false;
		}
	}
}
//************ GRUPMENU_ROL **************
$sql_del = 'TRUNCATE TABLE ref_grupmenu_rol RESTART IDENTITY';
if ($qRs = $oDevelPC->query($sql_del) === false) {
	$sClauError = 'ExportarMenu.VaciarTabla';
	$_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPC, $sClauError, __LINE__, __FILE__);
	return false;
}

$sQry = 'SELECT * FROM aux_grupmenu_rol';
foreach ( $oDevel->query($sQry,PDO::FETCH_ASSOC) as $aDades) { 
	unset($aDades['id_schema']);
	//print_r($aDades);
	$campos="(id_item,id_grupmenu,id_role)";
	$valores="(:id_item,:id_grupmenu,:id_role)";
	if (($qRs = $oDevelPC->prepare("INSERT INTO ref_grupmenu_rol $campos VALUES $valores")) === false) {
		$sClauError = 'Exposrtar.insertar.prepare';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPC, $sClauError, __LINE__, __FILE__);
		return false;
	} else {
		if ($qRs->execute($aDades) === false) {
			$sClauError = 'Exposrtar.insertar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPC, $sClauError, __LINE__, __FILE__);
			return false;
		}
	}
}
//************ MENUS **************
$sql_del = 'TRUNCATE TABLE ref_menus RESTART IDENTITY';
if ($qRs = $oDevelPC->query($sql_del) === false) {
	$sClauError = 'ExportarMenu.VaciarTabla';
	$_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPC, $sClauError, __LINE__, __FILE__);
	return false;
}

$sQry = "SELECT * FROM aux_menus WHERE ok='t'";
foreach ( $oDevel->query($sQry,PDO::FETCH_ASSOC) as $aDades) { 
	unset($aDades['id_schema']);
	//print_r($aDades);
	$campos="(id_menu,orden,menu,parametros,id_metamenu,menu_perm,id_grupmenu,ok)";
	$valores="(:id_menu,:orden,:menu,:parametros,:id_metamenu,:menu_perm,:id_grupmenu,:ok)";
	if (($qRs = $oDevelPC->prepare("INSERT INTO ref_menus $campos VALUES $valores")) === false) {
		$sClauError = 'Exportar.insertar.prepare';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPC, $sClauError, __LINE__, __FILE__);
		return false;
	} else {
		if ($qRs->execute($aDades) === false) {
			$sClauError = 'Exportar.insertar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPC, $sClauError, __LINE__, __FILE__);
			return false;
		}
	}
}

?>
