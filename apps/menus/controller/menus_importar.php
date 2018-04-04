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

$seguro = empty($_POST['seguro'])? 2 : $_POST['seguro'];
$todos = empty($_POST['todos'])? 2 : $_POST['todos'];

if ($seguro == 2) {
    if (core\ConfigGlobal::mi_dele() == 'dlb') {
        echo _("casi seguro que no quieres hacerlo.");
        echo "<br>";

        $go1=web\Hash::link('apps/menus/controller/menus_importar.php?'.http_build_query(array('seguro'=>1,'todos'=>1)));
        $html = "Esto pondrá los menus por defecto. Para todas las dl";
        $html = "tarda mucho, pero acaba bien (creo)";
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

if ($seguro == 1) {
    $aEsquemas = array('dl');
    if ($todos == 1) {
        $aDl = array('dlb','dlgr','dlmE','dlmO','dlp','dlst','dls','dlva','dlv','dlz');
        $aEsquemas = array();
        foreach ($aDl as $dl) {
            $aEsquemas[] = "H-".$dl."v";
            $aEsquemas[] = "H-".$dl."f";
        }
    }

    foreach ($aEsquemas as $esquema) {
        if ($todos == 2) { // solo un esquema
            //$oDB = $GLOBALS['oDB'];
        } else {
            echo ">>>>actualizando menus para $esquema<br>";
            $sec = substr($esquema,-1); // la v o la f.
            echo ">>>$sec>>actualizando menus para $esquema<br>";
            if ($sec == 'v') { $oDB = new \PDO(core\ConfigGlobal::get_conexio_sv($esquema)); }
            if ($sec == 'f') { $oDB = new \PDO(core\ConfigGlobal::get_conexio_sf($esquema)); }

            $oDB->exec("SET DATESTYLE TO '".core\ConfigGlobal::$datestyle."'");
            echo "actualizando menus para $esquema<br>";
        }

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