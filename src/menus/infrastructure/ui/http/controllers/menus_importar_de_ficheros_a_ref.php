<?php

use frontend\shared\config\OrbixRuntime;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\logging\GestorErrores;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\shared\web\ContestarJson;

// Copiar de dlb a public roles-grupmenu, grupmenu, menus
$oDBPC = GlobalPdo::get('oDBPC');
$gestorErrores = $_SESSION['oGestorErrores'] ?? null;
$messages = [];
$fail = static function (string $message): never {
    ContestarJson::enviar($message, 'none');
    exit;
};

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
    ContestarJson::enviar('', [
        'estado' => 'confirmacion',
        'puede_importar_todas' => OrbixRuntime::miDele() === 'dlb',
    ]);
    return;
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
        $messages[] = ">>>>actualizando menus para $esquema";
        $sec = substr($esquema, -1); // la 'v' o la 'f'.
        $messages[] = ">>>$sec>>actualizando menus para $esquema";
        if ($sec === 'v') {
            $oConfigDB = new ConfigDB('sv-e');
        } elseif ($sec === 'f') {
            $oConfigDB = new ConfigDB('sf-e');
        } else {
            $messages[] = "esquema desconocido: $esquema";
            continue;
        }
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);
        $oDB = $oConexion->getPDO();

        $messages[] = "actualizando menus para $esquema";

        //************ GRUPMENU **************
        $sql_del = 'TRUNCATE TABLE aux_grupmenu RESTART IDENTITY CASCADE';
        if ($oDB->query($sql_del) === false) {
            $sClauError = 'ExportarMenu.VaciarTabla';
            if ($gestorErrores instanceof GestorErrores) {
                $gestorErrores->addErrorAppLastError($oDB, $sClauError, (string) __LINE__, __FILE__);
            }
            $fail(_("No se han podido actualizar los menus"));
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
                    $fail(_("No se han podido actualizar los menus"));
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
                    $fail(_("No se han podido actualizar los menus"));
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
                $fail(_("No se han podido actualizar los menus"));
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
                        $fail(_("No se han podido actualizar los menus"));
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
                        $fail(_("No se han podido actualizar los menus"));
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
            $fail(_("No se han podido actualizar los menus"));
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
                    $fail(_("No se han podido actualizar los menus"));
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
                    $fail(_("No se han podido actualizar los menus"));
                }
            }
        }
    }
}

ContestarJson::enviar('', [
    'estado' => 'completado',
    'mensajes' => $messages,
]);
