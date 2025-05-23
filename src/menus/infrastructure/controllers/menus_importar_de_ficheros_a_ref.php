<?php

use core\ConfigGlobal;
use core\DBPropiedades;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// Copiar de dlb a public roles-grupmenu, grupmenu, menus
$oDB = $GLOBALS['oDBE'];
$oDBPC = $GLOBALS['oDBPC'];

$Qseguro = (integer)filter_input(INPUT_POST, 'seguro');
$Qtodos = (integer)filter_input(INPUT_POST, 'todos');

$Qseguro = empty($Qseguro) ? 2 : $Qseguro;
$Qtodos = empty($Qtodos) ? 2 : $Qtodos;

if ($Qseguro == 2) {
    if (ConfigGlobal::mi_dele() == 'dlb') {
        echo _("casi seguro que no quieres hacerlo");
        echo "<br>";

        $go1 = Hash::link('apps/menus/controller/menus_importar.php?' . http_build_query(array('seguro' => 1, 'todos' => 1)));
        $html = "Esto pondrá los menus por defecto. Para todas las dl";
        $html = "tarda mucho (3min para 10 dl), pero acaba bien (creo)";
        $html .= "<br>";
        $html .= "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go1');\">" . _("Poner todas las dl igual") . "</span>";
        $html .= "<br>";
        echo $html;
    }

    $go = Hash::link('apps/menus/controller/menus_importar.php?' . http_build_query(array('seguro' => 1)));
    $html = "Esto pondrá los menus por defecto. Se eliminaran todas las modificaciones que se hayan hecho en los menus y grupos de menu";
    $html .= "<br>";
    $html .= "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go');\">" . _("continuar") . "</span>";
    $html .= "<br><br><ul><li>";
    $html .= _("Para sf: también se copian los grupMenu de los Roles (de sv). Hay que volver a poner lo que había.");
    $html .= " ";
    $html .= _("De momento se ha anulado la restauración. Se queda como está") . ":<br>";
    $html .= _("Hay que corregir a mano");
    $html .= "</li></ul>";
    echo $html;
}

if ($Qseguro == 1) {
    $aEsquemas = [];
    if ($Qtodos == 1) {
        $oDBPropiedades = new DBPropiedades();
        $aEsquemas = $oDBPropiedades->array_posibles_esquemas();
    } else { // solo un esquema
        $mi_region_dl = ConfigGlobal::mi_region_dl();
        $aEsquemas[] = $mi_region_dl;
    }

    foreach ($aEsquemas as $esquema) {
        if ($esquema == "H-Hv") {
            continue;
        }
        echo ">>>>actualizando menus para $esquema<br>";
        $sec = substr($esquema, -1); // la 'v' o la 'f'.
        echo ">>>$sec>>actualizando menus para $esquema<br>";
        if ($sec == 'v') {
            $oConfigDB = new core\ConfigDB('sv-e');
        }
        if ($sec == 'f') {
            $oConfigDB = new core\ConfigDB('sf-e');

        }
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new core\DBConnection($config);
        $oDB = $oConexion->getPDO();

        echo "actualizando menus para $esquema<br>";

        //************ GRUPMENU **************
        $sql_del = 'TRUNCATE TABLE aux_grupmenu RESTART IDENTITY CASCADE';
        if ($oDblSt = $oDB->query($sql_del) === false) {
            $sClauError = 'ExportarMenu.VaciarTabla';
            $_SESSION['oGestorErrores']->addError('truncate', $sClauError, __LINE__, __FILE__);
            return false;
        }

        $sQry = 'SELECT * FROM ref_grupmenu';
        foreach ($oDBPC->query($sQry, PDO::FETCH_ASSOC) as $aDades) {
            //print_r($aDades);
            $campos = "(id_grupmenu,grup_menu,orden)";
            $valores = "(:id_grupmenu,:grup_menu,:orden)";
            if (($oDblSt = $oDB->prepare("INSERT INTO aux_grupmenu $campos VALUES $valores")) === false) {
                $sClauError = 'Importar.insertar.prepare';
                $_SESSION['oGestorErrores']->addError('prepare', $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $sClauError = 'Importar.insertar.execute';
                    $_SESSION['oGestorErrores']->addError($err_txt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        }
        //************ GRUPMENU_ROL**************
        // En el caso de la sf, los grupmenu asociados a los roles son distintos.
        // de momento no los copio. los dejo como están.
        if ($sec == 'v') {
            $sql_del = 'TRUNCATE TABLE aux_grupmenu_rol RESTART IDENTITY CASCADE';
            if ($oDblSt = $oDB->query($sql_del) === false) {
                $sClauError = 'ExportarMenu.VaciarTabla';
                $_SESSION['oGestorErrores']->addError('truncate', $sClauError, __LINE__, __FILE__);
                return false;
            }

            $sQry = 'SELECT * FROM ref_grupmenu_rol';
            foreach ($oDBPC->query($sQry, PDO::FETCH_ASSOC) as $aDades) {
                //print_r($aDades);
                $campos = "(id_item,id_grupmenu,id_role)";
                $valores = "(:id_item,:id_grupmenu,:id_role)";
                if (($oDblSt = $oDB->prepare("INSERT INTO aux_grupmenu_rol $campos VALUES $valores")) === false) {
                    $sClauError = 'Importar.insertar.prepare';
                    $_SESSION['oGestorErrores']->addError('prepare', $sClauError, __LINE__, __FILE__);
                    return false;
                } else {
                    try {
                        $oDblSt->execute($aDades);
                    } catch (PDOException $e) {
                        $err_txt = $e->errorInfo[2];
                        $sClauError = 'Importar.insertar.execute';
                        $_SESSION['oGestorErrores']->addError($err_txt, $sClauError, __LINE__, __FILE__);
                        return false;
                    }
                }
            }
        }
        //************ MENUS**************
        $sql_del = 'TRUNCATE TABLE aux_menus RESTART IDENTITY CASCADE';
        if ($oDblSt = $oDB->query($sql_del) === false) {
            $sClauError = 'ExportarMenu.VaciarTabla';
            $_SESSION['oGestorErrores']->addError('truncate', $sClauError, __LINE__, __FILE__);
            return false;
        }

        $sQry = 'SELECT * FROM ref_menus';
        foreach ($oDBPC->query($sQry, PDO::FETCH_ASSOC) as $aDades) {
            //print_r($aDades);
            $campos = "(id_menu,orden,menu,parametros,id_metamenu,menu_perm,id_grupmenu,ok)";
            $valores = "(:id_menu,:orden,:menu,:parametros,:id_metamenu,:menu_perm,:id_grupmenu,:ok)";
            if (($oDblSt = $oDB->prepare("INSERT INTO aux_menus $campos VALUES $valores")) === false) {
                $sClauError = 'Importar.insertar.prepare';
                $_SESSION['oGestorErrores']->addError('prepare', $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $sClauError = 'Importar.insertar.execute';
                    $_SESSION['oGestorErrores']->addError($err_txt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        }
    }
}
