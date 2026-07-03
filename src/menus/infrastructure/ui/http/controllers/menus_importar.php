<?php

use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\logging\GestorErrores;
use src\shared\web\ContestarJson;
$Qid_template_menu = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_template_menu');

$oDB = GlobalPdo::get('oDBE');
$oDBPC = GlobalPdo::get('oDBPC');
$gestorErrores = $_SESSION['oGestorErrores'] ?? null;

$error_txt = '';

//************ GRUPMENU **************
$sql_del = 'TRUNCATE TABLE aux_grupmenu RESTART IDENTITY CASCADE';
if ($oDB->query($sql_del) === false) {
    $sClauError = 'ExportarMenu.VaciarTabla';
    if ($gestorErrores instanceof GestorErrores) {
        $gestorErrores->addErrorAppLastError($oDB, $sClauError, (string) __LINE__, __FILE__);
    }
    $error_txt .= $sClauError;
}

$sQry = "SELECT id_grupmenu, grup_menu, orden FROM ref_grupmenu WHERE id_template_menu = $Qid_template_menu ";
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
            $error_txt .= $sClauError;
            continue;
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
            $error_txt .= $sClauError;
        }
    }
}
try {
    $sql_seq = "SELECT setval('aux_grupmenu_id_grupmenu_seq', (SELECT MAX(id_grupmenu) FROM aux_grupmenu))";
    $oDB->query($sql_seq);
} catch (PDOException $e) {
    $errorInfo = $e->errorInfo;
    $err_txt = is_array($errorInfo) && isset($errorInfo[2]) ? (string) $errorInfo[2] : $e->getMessage();
    $sClauError = 'Importar.sequence.execute';
    if ($gestorErrores instanceof GestorErrores) {
        $gestorErrores->addErrorAppLastErrorNoThrowText($err_txt, $sClauError, (string) __LINE__, __FILE__);
    }
    $error_txt .= $sClauError;
}

//************ GRUPMENU_ROL**************
$sql_del = 'TRUNCATE TABLE aux_grupmenu_rol RESTART IDENTITY CASCADE';
if ($oDB->query($sql_del) === false) {
    $sClauError = 'ExportarMenu.VaciarTabla';
    if ($gestorErrores instanceof GestorErrores) {
        $gestorErrores->addErrorAppLastError($oDB, $sClauError, (string) __LINE__, __FILE__);
    }
    $error_txt .= $sClauError;
}

$sQry = "SELECT id_item,id_grupmenu,id_role FROM ref_grupmenu_rol WHERE id_template_menu = $Qid_template_menu ";
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
            $error_txt .= $sClauError;
            continue;
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
            $error_txt .= $sClauError;
        }
    }
}
try {
    $sql_seq = "SELECT setval('aux_grupmenu_rol_id_item_seq', (SELECT MAX(id_item) FROM aux_grupmenu_rol))";
    $oDB->query($sql_seq);
} catch (PDOException $e) {
    $errorInfo = $e->errorInfo;
    $err_txt = is_array($errorInfo) && isset($errorInfo[2]) ? (string) $errorInfo[2] : $e->getMessage();
    $sClauError = 'Importar.sequence.execute';
    if ($gestorErrores instanceof GestorErrores) {
        $gestorErrores->addErrorAppLastErrorNoThrowText($err_txt, $sClauError, (string) __LINE__, __FILE__);
    }
    $error_txt .= $sClauError;
}

//************ MENUS**************
$sql_del = 'TRUNCATE TABLE aux_menus RESTART IDENTITY CASCADE';
if ($oDB->query($sql_del) === false) {
    $sClauError = 'ExportarMenu.VaciarTabla';
    if ($gestorErrores instanceof GestorErrores) {
        $gestorErrores->addErrorAppLastError($oDB, $sClauError, (string) __LINE__, __FILE__);
    }
    $error_txt .= $sClauError;
}

$sQry = "SELECT id_menu,orden,menu,parametros,id_metamenu,menu_perm,id_grupmenu,ok FROM ref_menus WHERE id_template_menu = $Qid_template_menu ";
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
            $error_txt .= $sClauError;
            continue;
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
            $error_txt .= $sClauError;
        }
    }
}
try {
    $sql_seq = "SELECT setval('aux_menus_id_menu_seq', (SELECT MAX(id_menu) FROM aux_menus))";
    $oDB->query($sql_seq);
} catch (PDOException $e) {
    $errorInfo = $e->errorInfo;
    $err_txt = is_array($errorInfo) && isset($errorInfo[2]) ? (string) $errorInfo[2] : $e->getMessage();
    $sClauError = 'Importar.sequence.execute';
    if ($gestorErrores instanceof GestorErrores) {
        $gestorErrores->addErrorAppLastErrorNoThrowText($err_txt, $sClauError, (string) __LINE__, __FILE__);
    }
    $error_txt .= $sClauError;
}

ContestarJson::enviar($error_txt, 'ok');
