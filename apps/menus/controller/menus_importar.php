<?php

use core\DBPropiedades;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// Copiar de dlb a public roles-grupmenu, grupmenu, menus
$oDB = $GLOBALS['oDB'];
$oDBPC = $GLOBALS['oDBPC'];

$Qseguro = (integer) \filter_input(INPUT_POST, 'seguro');
$Qtodos = (integer) \filter_input(INPUT_POST, 'todos');

$Qseguro = empty($Qseguro)? 2 : $Qseguro;
$Qtodos = empty($Qtodos)? 2 : $Qtodos;

if ($Qseguro == 2) {
    if (core\ConfigGlobal::mi_dele() == 'dlb') {
        echo _("casi seguro que no quieres hacerlo");
        echo "<br>";

        $go1=web\Hash::link('apps/menus/controller/menus_importar.php?'.http_build_query(array('seguro'=>1,'todos'=>1)));
        $html = "Esto pondrá los menus por defecto. Para todas las dl";
        $html = "tarda mucho (3min para 10 dl), pero acaba bien (creo)";
        $html .= "<br>";
        $html .= "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go1');\">". _("Poner todas las dl igual")."</span>";
        $html .= "<br>";
        echo $html;
    }

    $go=web\Hash::link('apps/menus/controller/menus_importar.php?'.http_build_query(array('seguro'=>1)));
    $html = "Esto pondrá los menus por defecto. Se eliminaran todas las modificaciones que se hayan hecho en los menus y grupos de menu";
    $html .= "<br>";
    $html .= "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go');\">". _("continuar")."</span>";
    echo $html;
}

if ($Qseguro == 1) {
	$aEsquemas = array();
    if ($Qtodos == 1) {
        $oDBPropiedades = new DBPropiedades();
        $aEsquemas = $oDBPropiedades->array_posibles_esquemas();
    } else { // solo un esquema
		$mi_region_dl = core\ConfigGlobal::mi_region_dl();
		$aEsquemas[] = $mi_region_dl;
	}

    foreach ($aEsquemas as $esquema) {
		echo ">>>>actualizando menus para $esquema<br>";
		$sec = substr($esquema,-1); // la v o la f.
		echo ">>>$sec>>actualizando menus para $esquema<br>";
		if ($sec == 'v') { 
			$oConfigDB = new core\ConfigDB('sv'); 
		}
		if ($sec == 'f') {
			$oConfigDB = new core\ConfigDB('sf'); 
			
		}
		$config = $oConfigDB->getEsquema($esquema); 
		$oConexion = new core\dbConnection($config);
		$oDB = $oConexion->getPDO();

		echo "actualizando menus para $esquema<br>";

        //************ GRUPMENU **************
		$sql_del = 'TRUNCATE TABLE aux_grupmenu RESTART IDENTITY CASCADE';
		if ($oDblSt = $oDB->query($sql_del) === false) {
			$sClauError = 'ExportarMenu.VaciarTabla';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return false;
		}

		$sQry = 'SELECT * FROM ref_grupmenu';
		foreach ( $oDBPC->query($sQry,\PDO::FETCH_ASSOC) as $aDades) { 
			//print_r($aDades);
			$campos="(id_grupmenu,grup_menu,orden)";
			$valores="(:id_grupmenu,:grup_menu,:orden)";
			if (($oDblSt = $oDB->prepare("INSERT INTO aux_grupmenu $campos VALUES $valores")) === false) {
				$sClauError = 'Importar.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Importar.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		}
		//************ GRUPMENU_ROL**************
		$sql_del = 'TRUNCATE TABLE aux_grupmenu_rol RESTART IDENTITY CASCADE';
		if ($oDblSt = $oDB->query($sql_del) === false) {
			$sClauError = 'ExportarMenu.VaciarTabla';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return false;
		}

		$sQry = 'SELECT * FROM ref_grupmenu_rol';
		foreach ( $oDBPC->query($sQry,\PDO::FETCH_ASSOC) as $aDades) { 
			//print_r($aDades);
			$campos="(id_item,id_grupmenu,id_role)";
			$valores="(:id_item,:id_grupmenu,:id_role)";
			if (($oDblSt = $oDB->prepare("INSERT INTO aux_grupmenu_rol $campos VALUES $valores")) === false) {
				$sClauError = 'Importar.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Importar.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		}
		//************ MENUS**************
		$sql_del = 'TRUNCATE TABLE aux_menus RESTART IDENTITY CASCADE';
		if ($oDblSt = $oDB->query($sql_del) === false) {
			$sClauError = 'ExportarMenu.VaciarTabla';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return false;
		}

		$sQry = 'SELECT * FROM ref_menus';
		foreach ( $oDBPC->query($sQry,\PDO::FETCH_ASSOC) as $aDades) { 
			//print_r($aDades);
			$campos="(id_menu,orden,menu,parametros,id_metamenu,menu_perm,id_grupmenu,ok)";
			$valores="(:id_menu,:orden,:menu,:parametros,:id_metamenu,:menu_perm,:id_grupmenu,:ok)";
			if (($oDblSt = $oDB->prepare("INSERT INTO aux_menus $campos VALUES $valores")) === false) {
				$sClauError = 'Importar.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Importar.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		}
	}
}
