<?php
/*
 * Se queda en la capa de infraestructura porque ataca directamente a la base de datos !!!!
 */

use src\menus\domain\contracts\TemplateMenuRepositoryInterface;
use src\menus\domain\entity\TemplateMenu;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\logging\GestorErrores;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

$Qnombre = input_string($_POST, 'nombre');
$Qsobreescribir = input_string($_POST, 'sobreescribir');

$error_txt = '';

/** @var TemplateMenuRepositoryInterface $TemplateMenuRepository */
$TemplateMenuRepository = DependencyResolver::get(TemplateMenuRepositoryInterface::class);
$oTemplateMenu = $TemplateMenuRepository->findByName($Qnombre);
if ($oTemplateMenu !== null && !is_true($Qsobreescribir)) {
    ContestarJson::enviar('ya existe', 'ok');
    exit();
}

if ($oTemplateMenu !== null) {
    $id_template_menu = $oTemplateMenu->getId_template_menu();
} else {
    $id_template_menu = $TemplateMenuRepository->getNewId();
    $oTemplateMenu = new TemplateMenu();
    $oTemplateMenu->setId_template_menu($id_template_menu);
    $oTemplateMenu->setNombreVo($Qnombre);
    $TemplateMenuRepository->Guardar($oTemplateMenu);
}

$oDevel = GlobalPdo::get('oDBE');
$oDevelPC = GlobalPdo::get('oDBPC');
$gestorErrores = $_SESSION['oGestorErrores'] ?? null;
$sql_del = "DELETE FROM ref_grupmenu WHERE id_template_menu = $id_template_menu";
if ($oDevelPC->query($sql_del) === false) {
    $sClauError = 'ExportarMenu.VaciarTabla';
    if ($gestorErrores instanceof GestorErrores) {
        $gestorErrores->addErrorAppLastError($oDevelPC, $sClauError, (string) __LINE__, __FILE__);
    }
    $error_txt .= $sClauError;
}

$sQry = 'SELECT * FROM aux_grupmenu';
$grupMenuRows = $oDevel->query($sQry, PDO::FETCH_ASSOC);
if ($grupMenuRows !== false) {
    foreach ($grupMenuRows as $aDades) {
        if (!is_array($aDades)) {
            continue;
        }
        unset($aDades['id_schema']);
        $campos = "(id_grupmenu,grup_menu,orden,id_template_menu)";
        $valores = "(:id_grupmenu,:grup_menu,:orden,$id_template_menu)";
        $oDevelPCSt = $oDevelPC->prepare("INSERT INTO ref_grupmenu $campos VALUES $valores");
        if ($oDevelPCSt === false) {
            $sClauError = 'Exportar.insertar.prepare';
            if ($gestorErrores instanceof GestorErrores) {
                $gestorErrores->addErrorAppLastError($oDevelPC, $sClauError, (string) __LINE__, __FILE__);
            }
            $error_txt .= $sClauError;
            continue;
        }

        try {
            $oDevelPCSt->execute($aDades);
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo;
            $error_txt .= is_array($errorInfo) && isset($errorInfo[2]) ? (string) $errorInfo[2] : $e->getMessage();
            $sClauError = 'Exposrtar.insertar.execute';
            if ($gestorErrores instanceof GestorErrores) {
                $gestorErrores->addErrorAppLastError($oDevelPCSt, $sClauError, (string) __LINE__, __FILE__);
            }
        }
    }
}
//************ GRUPMENU_ROL **************
$sql_del = "DELETE FROM ref_grupmenu_rol WHERE id_template_menu = $id_template_menu";
if ($oDevelPC->query($sql_del) === false) {
    $sClauError = 'ExportarMenu.VaciarTabla';
    if ($gestorErrores instanceof GestorErrores) {
        $gestorErrores->addErrorAppLastError($oDevelPC, $sClauError, (string) __LINE__, __FILE__);
    }
    $error_txt .= $sClauError;
}

$sQry = 'SELECT * FROM aux_grupmenu_rol';
$grupMenuRolRows = $oDevel->query($sQry, PDO::FETCH_ASSOC);
if ($grupMenuRolRows !== false) {
    foreach ($grupMenuRolRows as $aDades) {
        if (!is_array($aDades)) {
            continue;
        }
        unset($aDades['id_schema']);
        $campos = "(id_item,id_grupmenu,id_role,id_template_menu)";
        $valores = "(:id_item,:id_grupmenu,:id_role,$id_template_menu)";
        $oDevelPCSt = $oDevelPC->prepare("INSERT INTO ref_grupmenu_rol $campos VALUES $valores");
        if ($oDevelPCSt === false) {
            $sClauError = 'Exportar.insertar.prepare';
            if ($gestorErrores instanceof GestorErrores) {
                $gestorErrores->addErrorAppLastError($oDevelPC, $sClauError, (string) __LINE__, __FILE__);
            }
            $error_txt .= $sClauError;
            continue;
        }

        try {
            $oDevelPCSt->execute($aDades);
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo;
            $error_txt .= is_array($errorInfo) && isset($errorInfo[2]) ? (string) $errorInfo[2] : $e->getMessage();
            $sClauError = 'Exportar.insertar.execute';
            if ($gestorErrores instanceof GestorErrores) {
                $gestorErrores->addErrorAppLastError($oDevelPCSt, $sClauError, (string) __LINE__, __FILE__);
            }
        }
    }
}
//************ MENUS **************
$sql_del = "DELETE FROM ref_menus WHERE id_template_menu = $id_template_menu";
if ($oDevelPC->query($sql_del) === false) {
    $sClauError = 'ExportarMenu.VaciarTabla';
    if ($gestorErrores instanceof GestorErrores) {
        $gestorErrores->addErrorAppLastError($oDevelPC, $sClauError, (string) __LINE__, __FILE__);
    }
    $error_txt .= $sClauError;
}

$sQry = "SELECT * FROM aux_menus WHERE ok='t'";
$menuRows = $oDevel->query($sQry, PDO::FETCH_ASSOC);
if ($menuRows !== false) {
    foreach ($menuRows as $aDades) {
        if (!is_array($aDades)) {
            continue;
        }
        unset($aDades['id_schema']);
        $campos = "(id_menu,orden,menu,parametros,id_metamenu,menu_perm,id_grupmenu,ok,id_template_menu)";
        $valores = "(:id_menu,:orden,:menu,:parametros,:id_metamenu,:menu_perm,:id_grupmenu,:ok,$id_template_menu)";
        $oDevelPCSt = $oDevelPC->prepare("INSERT INTO ref_menus $campos VALUES $valores");
        if ($oDevelPCSt === false) {
            $sClauError = 'Exportar.insertar.prepare';
            if ($gestorErrores instanceof GestorErrores) {
                $gestorErrores->addErrorAppLastError($oDevelPC, $sClauError, (string) __LINE__, __FILE__);
            }
            $error_txt .= $sClauError;
            continue;
        }

        try {
            $oDevelPCSt->execute($aDades);
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo;
            $error_txt .= is_array($errorInfo) && isset($errorInfo[2]) ? (string) $errorInfo[2] : $e->getMessage();
            $sClauError = 'Exportar.insertar.execute';
            if ($gestorErrores instanceof GestorErrores) {
                $gestorErrores->addErrorAppLastError($oDevelPCSt, $sClauError, (string) __LINE__, __FILE__);
            }
        }
    }
}

ContestarJson::enviar($error_txt, 'ok');
