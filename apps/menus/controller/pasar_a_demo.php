<?php
use menus\model as menus;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// Copiar de dlb a public roles-grupmenu, grupmenu, menus
$oDevel = $GLOBALS['oDB'];
$oDevelPC = $GLOBALS['oDBPC'];

//Conexiones a demo.moneders.net
$str_conexio_public="pgsql:host=demo.moneders.net sslmode=require port=5432  dbname='comun' user='orbix' password='orbix'";
$str_conexio_publicv="pgsql:host=demo.moneders.net sslmode=require port=5432  dbname='sv' user='orbixv' password='orbixv'";
$str_conexio_publicf="pgsql:host=demo.moneders.net sslmode=require port=5432  dbname='sf' user='orbixf' password='orbixf'";

$str_conexio_resto="pgsql:host=demo.moneders.net sslmode=require port=5432  dbname='comun' user='orbix' password='orbix'";
$str_conexio_restov="pgsql:host=demo.moneders.net sslmode=require port=5432  dbname='sv' user='orbixv' password='orbixv'";
$str_conexio_restof="pgsql:host=demo.moneders.net sslmode=require port=5432  dbname='sf' user='orbixf' password='orbixf'";

//$str = "pgsql:host=localhost port=5432  dbname='comun' user='$reg' password='$reg'";

//Definir Conexiones
// public para todo el muindo
$oDemoPC = new \PDO($str_conexio_public);
$oDemoRC = new \PDO($str_conexio_resto);
//comun
//$oDemoC = new \PDO(get_conexio_comu());
$user_sfsv =$_SESSION['session_auth']['sfsv']; 
switch ($user_sfsv) {
	case 1: //sv
		//$oDemo = new \PDO(get_conexio_sv());
		$oDemoP = new \PDO($str_conexio_publicv);
		$oDemoR = new \PDO($str_conexio_restov);
		//sf
		//$oDemoE = new \PDO(get_conexio_sf());
		$oDemoEP = new \PDO($str_conexio_publicf);
		$oDemoER = new \PDO($str_conexio_restof);
		break;
	case 2: //sf
		//$oDemo = new \PDO(get_conexio_sf());
		$oDemoP = new \PDO($str_conexio_publicf);
		$oDemoR = new \PDO($str_conexio_restof);
		//sv
		//$oDemoE = new \PDO(get_conexio_sv());
		break;
}


//************ METAMENUS **************
$sql_del = 'TRUNCATE TABLE "public".aux_metamenus RESTART IDENTITY';
if ($qRs = $oDemoPC->query($sql_del) === false) {
	$sClauError = 'ExportarMenu.VaciarTabla';
	$_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPC, $sClauError, __LINE__, __FILE__);
	return false;
}

$sQry = 'SELECT * FROM "public".aux_metamenus';
foreach ( $oDevelPC->query($sQry,\PDO::FETCH_ASSOC) as $aDades) { 
	//print_r($aDades);
	$campos="(id_metamenu,modulo,url,parametros,descripcion)";
	$valores="(:id_metamenu,:modulo,:url,:parametros,:descripcion)";
	if (($qRs = $oDemoPC->prepare("INSERT INTO public.aux_metamenus $campos VALUES $valores")) === false) {
		$sClauError = 'PassarADemo.insertar.prepare';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDemoPC, $sClauError, __LINE__, __FILE__);
		return false;
	} else {
		if ($qRs->execute($aDades) === false) {
			$sClauError = 'PassarADemo.insertar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDemoPC, $sClauError, __LINE__, __FILE__);
			return false;
		}
	}
}
//************ GRUPMENU **************
$sql_del = 'TRUNCATE TABLE "public".ref_grupmenu RESTART IDENTITY';
if ($qRs = $oDemoPC->query($sql_del) === false) {
	$sClauError = 'ExportarMenu.VaciarTabla';
	$_SESSION['oGestorErrores']->addErrorAppLastError($oDemoPC, $sClauError, __LINE__, __FILE__);
	return false;
}

$sQry = 'SELECT * FROM "public".ref_grupmenu';
foreach ( $oDevelPC->query($sQry,\PDO::FETCH_ASSOC) as $aDades) { 
	//print_r($aDades);
	$campos="(id_grupmenu,grup_menu,orden)";
	$valores="(:id_grupmenu,:grup_menu,:orden)";
	if (($qRs = $oDemoPC->prepare("INSERT INTO public.ref_grupmenu $campos VALUES $valores")) === false) {
		$sClauError = 'PassarADemo.insertar.prepare';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDemoPC, $sClauError, __LINE__, __FILE__);
		return false;
	} else {
		if ($qRs->execute($aDades) === false) {
			$sClauError = 'PassarADemo.insertar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDemoPC, $sClauError, __LINE__, __FILE__);
			return false;
		}
	}
}
//************ GRUPMENU_ROL **************
$sql_del = 'TRUNCATE TABLE "public".ref_grupmenu_rol RESTART IDENTITY';
if ($qRs = $oDemoPC->query($sql_del) === false) {
	$sClauError = 'ExportarMenu.VaciarTabla';
	$_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPC, $sClauError, __LINE__, __FILE__);
	return false;
}

$sQry = 'SELECT * FROM "public".ref_grupmenu_rol';
foreach ( $oDevelPC->query($sQry,\PDO::FETCH_ASSOC) as $aDades) { 
	//print_r($aDades);
	$campos="(id_item,id_grupmenu,id_role)";
	$valores="(:id_item,:id_grupmenu,:id_role)";
	if (($qRs = $oDemoPC->prepare("INSERT INTO public.ref_grupmenu_rol $campos VALUES $valores")) === false) {
		$sClauError = 'PassarADemo.insertar.prepare';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDemoPC, $sClauError, __LINE__, __FILE__);
		return false;
	} else {
		if ($qRs->execute($aDades) === false) {
			$sClauError = 'PassarADemo.insertar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDemoPC, $sClauError, __LINE__, __FILE__);
			return false;
		}
	}
}

//************ MENUS **************
$sql_del = 'TRUNCATE TABLE "public".ref_menus RESTART IDENTITY';
if ($qRs = $oDemoPC->query($sql_del) === false) {
	$sClauError = 'ExportarMenu.VaciarTabla';
	$_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPC, $sClauError, __LINE__, __FILE__);
	return false;
}

$sQry = 'SELECT * FROM "public".ref_menus';
foreach ( $oDevelPC->query($sQry,\PDO::FETCH_ASSOC) as $aDades) { 
	//print_r($aDades);
	$campos="(id_menu,orden,menu,parametros,id_metamenu,menu_perm,id_grupmenu,ok)";
	$valores="(:id_menu,:orden,:menu,:parametros,:id_metamenu,:menu_perm,:id_grupmenu,:ok)";
	if (($qRs = $oDemoPC->prepare("INSERT INTO public.ref_menus $campos VALUES $valores")) === false) {
		$sClauError = 'PassarADemo.insertar.prepare';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDemoPC, $sClauError, __LINE__, __FILE__);
		return false;
	} else {
		if ($qRs->execute($aDades) === false) {
			$sClauError = 'PassarADemo.insertar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDemoPC, $sClauError, __LINE__, __FILE__);
			return false;
		}
	}
}

/*

$sql_del = 'TRUNCATE TABLE aux_menus RESTART IDENTITY';
if ($qRs = $oDbl->query($sql_del) === false) {
	$sClauError = 'ImportarMenu.VaciarTabla';
	$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	return false;
}

$sql_copy = 'INSERT INTO aux_menus SELECT * FROM "public".ref_menus';
if ($qRs = $oDbl->query($sql_copy) === false) {
	$sClauError = 'ImportarMenu.LlenarTabla';
	$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	return false;
}
*/
?>
