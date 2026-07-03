<?php

use frontend\shared\config\OrbixRuntime;
use frontend\shared\security\HashFront;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\logging\GestorErrores;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\shared\domain\helpers\FilterPostGet;

// Copiar de dlb a public roles-grupmenu, grupmenu, menus
$oDBPC = GlobalPdo::get('oDBPC');
$gestorErrores = $_SESSION['oGestorErrores'] ?? null;

$Qseguro = \src\shared\domain\helpers\FilterPostGet::post('seguro', FILTER_VALIDATE_INT);
if ($Qseguro === false || $Qseguro === null) {
    $Qseguro = \src\shared\domain\helpers\FilterPostGet::get('seguro', FILTER_VALIDATE_INT);
}
$Qtodos = \src\shared\domain\helpers\FilterPostGet::post('todos', FILTER_VALIDATE_INT);
if ($Qtodos === false || $Qtodos === null) {
    $Qtodos = \src\shared\domain\helpers\FilterPostGet::get('todos', FILTER_VALIDATE_INT);
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
    if (!is_array($aEsquemas)) {
        $aEsquemas = [];
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
        if ($oDB->query($sql_del) === false) {
            $sClauError = 'ExportarMenu.VaciarTabla';
            if ($gestorErrores instanceof GestorErrores) {
                $gestorErrores->addErrorAppLastError($oDB, $sClauError, (string) __LINE__, __FILE__);
            }
            return false;
        }

        $sQry = 'SELECT * FROM ref_grupmenu';
        $grupMenuRows = $oDBPC->query($sQry, PDO::FETCH_ASSOC);
        if ($grupMenuRows !== false) {
            foreach ($grupMenuRows as $aDades) {
                if (!is_array($aDades)) {
                    continue;
                }
                $campos = "(id_grupmenu,grup_menu,orden)";
                $valores = "(:id_grupmenu,:grup_menu,:orden)";
                $oDblSt = $oDB->prepare("INSERT INTO aux_grupmenu $campos VALUES $valores");
                if ($oDblSt === false) {
                    $sClauError = 'Importar.insertar.prepare';
                    if ($gestorErrores instanceof GestorErrores) {
                        $gestorErrores->addErrorAppLastError($oDB, $sClauError, (string) __LINE__, __FILE__);
                    }
                    return false;
                }

                try {
                    $oDblSt->execute($aDades);
                } catch (PDOException $e) {
                    $errorInfo = $e->errorInfo;
                    $err_txt = is_array($errorInfo) && isset($errorInfo[2]) ? (string) $errorInfo[2] : $e->getMessage();
                    $sClauError = 'Importar.insertar.execute';
                    if ($gestorErrores instanceof GestorErrores) {
                        $gestorErrores->addErrorAppLastErrorNoThrowText($err_txt, $sClauError, (string) __LINE__, __FILE__);
                    }
                    return false;
                }
            }
        }
        //************ GRUPMENU_ROL**************
        // En el caso de la sf, los grupmenu asociados a los roles son distintos.
        // de momento no los copio. los dejo como están.
        if ($sec === 'v') {
            $sql_del = 'TRUNCATE TABLE aux_grupmenu_rol RESTART IDENTITY CASCADE';
            if ($oDB->query($sql_del) === false) {
                $sClauError = 'ExportarMenu.VaciarTabla';
                if ($gestorErrores instanceof GestorErrores) {
                    $gestorErrores->addErrorAppLastError($oDB, $sClauError, (string) __LINE__, __FILE__);
                }
                return false;
            }

            $sQry = 'SELECT * FROM ref_grupmenu_rol';
            $grupMenuRolRows = $oDBPC->query($sQry, PDO::FETCH_ASSOC);
            if ($grupMenuRolRows !== false) {
                foreach ($grupMenuRolRows as $aDades) {
                    if (!is_array($aDades)) {
                        continue;
                    }
                    $campos = "(id_item,id_grupmenu,id_role)";
                    $valores = "(:id_item,:id_grupmenu,:id_role)";
                    $oDblSt = $oDB->prepare("INSERT INTO aux_grupmenu_rol $campos VALUES $valores");
                    if ($oDblSt === false) {
                        $sClauError = 'Importar.insertar.prepare';
                        if ($gestorErrores instanceof GestorErrores) {
                            $gestorErrores->addErrorAppLastError($oDB, $sClauError, (string) __LINE__, __FILE__);
                        }
                        return false;
                    }

                    try {
                        $oDblSt->execute($aDades);
                    } catch (PDOException $e) {
                        $errorInfo = $e->errorInfo;
                        $err_txt = is_array($errorInfo) && isset($errorInfo[2]) ? (string) $errorInfo[2] : $e->getMessage();
                        $sClauError = 'Importar.insertar.execute';
                        if ($gestorErrores instanceof GestorErrores) {
                            $gestorErrores->addErrorAppLastErrorNoThrowText($err_txt, $sClauError, (string) __LINE__, __FILE__);
                        }
                        return false;
                    }
                }
            }
        }
        //************ MENUS**************
        $sql_del = 'TRUNCATE TABLE aux_menus RESTART IDENTITY CASCADE';
        if ($oDB->query($sql_del) === false) {
            $sClauError = 'ExportarMenu.VaciarTabla';
            if ($gestorErrores instanceof GestorErrores) {
                $gestorErrores->addErrorAppLastError($oDB, $sClauError, (string) __LINE__, __FILE__);
            }
            return false;
        }

        $sQry = 'SELECT * FROM ref_menus';
        $menuRows = $oDBPC->query($sQry, PDO::FETCH_ASSOC);
        if ($menuRows !== false) {
            foreach ($menuRows as $aDades) {
                if (!is_array($aDades)) {
                    continue;
                }
                $campos = "(id_menu,orden,menu,parametros,id_metamenu,menu_perm,id_grupmenu,ok)";
                $valores = "(:id_menu,:orden,:menu,:parametros,:id_metamenu,:menu_perm,:id_grupmenu,:ok)";
                $oDblSt = $oDB->prepare("INSERT INTO aux_menus $campos VALUES $valores");
                if ($oDblSt === false) {
                    $sClauError = 'Importar.insertar.prepare';
                    if ($gestorErrores instanceof GestorErrores) {
                        $gestorErrores->addErrorAppLastError($oDB, $sClauError, (string) __LINE__, __FILE__);
                    }
                    return false;
                }

                try {
                    $oDblSt->execute($aDades);
                } catch (PDOException $e) {
                    $errorInfo = $e->errorInfo;
                    $err_txt = is_array($errorInfo) && isset($errorInfo[2]) ? (string) $errorInfo[2] : $e->getMessage();
                    $sClauError = 'Importar.insertar.execute';
                    if ($gestorErrores instanceof GestorErrores) {
                        $gestorErrores->addErrorAppLastErrorNoThrowText($err_txt, $sClauError, (string) __LINE__, __FILE__);
                    }
                    return false;
                }
            }
        }
    }
}
