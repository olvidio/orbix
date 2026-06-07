<?php

use frontend\shared\config\OrbixRuntime;
use frontend\shared\security\HashFront;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;

// Copiar de dlb a public roles-grupmenu, grupmenu, menus
$oDBPC = GlobalPdo::get('oDBPC');

$Qseguro = filter_input(INPUT_POST, 'seguro', FILTER_VALIDATE_INT);
if ($Qseguro === false || $Qseguro === null) {
    $Qseguro = filter_input(INPUT_GET, 'seguro', FILTER_VALIDATE_INT);
}
$Qtodos = filter_input(INPUT_POST, 'todos', FILTER_VALIDATE_INT);
if ($Qtodos === false || $Qtodos === null) {
    $Qtodos = filter_input(INPUT_GET, 'todos', FILTER_VALIDATE_INT);
}

$Qseguro = ($Qseguro === false || $Qseguro === null || $Qseguro === 0) ? 2 : $Qseguro;
$Qtodos = ($Qtodos === false || $Qtodos === null || $Qtodos === 0) ? 2 : $Qtodos;

if ($Qseguro === 2) {
    if (OrbixRuntime::miDele() === 'dlb') {
        echo _("casi seguro que no quieres hacerlo");
        echo "<br>";

        $go1 = HashFront::link('src/menus/menus_importar_de_ficheros_a_ref?' . http_build_query(array('seguro' => 1, 'todos' => 1)));
        $html = "Esto pondrá los menus por defecto. Para todas las dl";
        $html .= "tarda mucho (3min para 10 dl), pero acaba bien (creo)";
        $html .= "<br>";
        $html .= "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go1');\">" . _("Poner todas las dl igual") . "</span>";
        $html .= "<br>";
        echo $html;
    }

    $go = HashFront::link('src/menus/menus_importar_de_ficheros_a_ref?' . http_build_query(array('seguro' => 1)));
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

if ($Qseguro === 1) {
    $aEsquemas = [];
    if ($Qtodos === 1) {
        $oDBPropiedades = new DBPropiedades();
        $aEsquemas = $oDBPropiedades->array_posibles_esquemas();
    } else { // solo un esquema
        $mi_region_dl = OrbixRuntime::miRegionDl();
        $aEsquemas[] = $mi_region_dl;
    }

    foreach ($aEsquemas as $esquema) {
        if ($esquema === "H-Hv") {
            continue;
        }
        echo ">>>>actualizando menus para $esquema<br>";
        $sec = substr($esquema, -1); // la 'v' o la 'f'.
        echo ">>>$sec>>actualizando menus para $esquema<br>";
        if ($sec === 'v') {
            $oConfigDB = new ConfigDB('sv-e');
        } elseif ($sec === 'f') {
            $oConfigDB = new ConfigDB('sf-e');
        } else {
            echo "esquema desconocido: $esquema<br>";
            continue;
        }
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);
        $oDB = $oConexion->getPDO();

        echo "actualizando menus para $esquema<br>";

        //************ GRUPMENU **************
        $sql_del = 'TRUNCATE TABLE aux_grupmenu RESTART IDENTITY CASCADE';
        if (($oDblSt = $oDB->query($sql_del)) === false) {
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
        if ($sec === 'v') {
            $sql_del = 'TRUNCATE TABLE aux_grupmenu_rol RESTART IDENTITY CASCADE';
            if (($oDblSt = $oDB->query($sql_del)) === false) {
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
        if (($oDblSt = $oDB->query($sql_del)) === false) {
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
