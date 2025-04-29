<?php
/*
 * Se queda en la capa de infraestructura porque ataca directamente a la base de datos !!!!
 *
 */
use src\menus\application\repositories\TemplateMenuRepository;
use src\menus\domain\entity\TemplateMenu;
use web\ContestarJson;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qnombre = (string)filter_input(INPUT_POST, 'nombre');
$Qsobreescribir = (string)filter_input(INPUT_POST, 'sobreescribir');

$error_txt = '';

// Comprobar si ya existe el nombre y si se ha se sobre-escribir
$TemplateMenuRepository = new TemplateMenuRepository();
$oTemplateMenu = $TemplateMenuRepository->findByName($Qnombre);
if (!empty($oTemplateMenu) && !is_true($Qsobreescribir)) {
    $id_template_menu = $oTemplateMenu->getId_template_menu();
    $error_txt = 'ya existe';
    ContestarJson::enviar($error_txt, 'ok');
    exit();
}

if (!empty($oTemplateMenu)) {
    $id_template_menu = $oTemplateMenu->getId_template_menu();
} else { // crear uno nuevo
    $id_template_menu = $TemplateMenuRepository->getNewId();
    $oTemplateMenu = new TemplateMenu();
    $oTemplateMenu->setId_template_menu($id_template_menu);
    $oTemplateMenu->setNombre($Qnombre);
    $TemplateMenuRepository->Guardar($oTemplateMenu);
}

// Copiar del esquema actual a public roles-grupmenu, grupmenu, menus
$oDevel = $GLOBALS['oDBE'];
$oDevelPC = $GLOBALS['oDBPC'];

//************ GRUPMENU **************
$sql_del = "DELETE FROM ref_grupmenu WHERE id_template_menu = $id_template_menu";
if (($oDevelPCSt = $oDevelPC->query($sql_del)) === false) {
    $sClauError = 'ExportarMenu.VaciarTabla';
    $_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPCSt, $sClauError, __LINE__, __FILE__);
    $error_txt .= $sClauError;
}

$sQry = 'SELECT * FROM aux_grupmenu';
foreach ($oDevel->query($sQry, PDO::FETCH_ASSOC) as $aDades) {
    unset($aDades['id_schema']);
    //print_r($aDades);
    $campos = "(id_grupmenu,grup_menu,orden,id_template_menu)";
    $valores = "(:id_grupmenu,:grup_menu,:orden,$id_template_menu)";
    if (($oDevelPCSt = $oDevelPC->prepare("INSERT INTO ref_grupmenu $campos VALUES $valores")) === false) {
        $sClauError = 'Exportar.insertar.prepare';
        $_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPCSt, $sClauError, __LINE__, __FILE__);
        $error_txt .= $sClauError;
    }

    try {
        $oDevelPCSt->execute($aDades);
    } catch (PDOException $e) {
        $error_txt .= $e->errorInfo[2];
        $sClauError = 'Exposrtar.insertar.execute';
        $_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPCSt, $sClauError, __LINE__, __FILE__);
    }
}
//************ GRUPMENU_ROL **************
$sql_del = "DELETE FROM ref_grupmenu_rol WHERE id_template_menu = $id_template_menu";
if (($oDevelPCSt = $oDevelPC->query($sql_del)) === false) {
    $sClauError = 'ExportarMenu.VaciarTabla';
    $_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPCSt, $sClauError, __LINE__, __FILE__);
    $error_txt .= $sClauError;
}

$sQry = 'SELECT * FROM aux_grupmenu_rol';
foreach ($oDevel->query($sQry, PDO::FETCH_ASSOC) as $aDades) {
    unset($aDades['id_schema']);
    //print_r($aDades);
    $campos = "(id_item,id_grupmenu,id_role,id_template_menu)";
    $valores = "(:id_item,:id_grupmenu,:id_role,$id_template_menu)";
    if (($oDevelPCSt = $oDevelPC->prepare("INSERT INTO ref_grupmenu_rol $campos VALUES $valores")) === false) {
        $sClauError = 'Exportar.insertar.prepare';
        $_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPCSt, $sClauError, __LINE__, __FILE__);
        $error_txt .= $sClauError;
    }

    try {
        $oDevelPCSt->execute($aDades);
    } catch (PDOException $e) {
        $error_txt .= $e->errorInfo[2];
        $sClauError = 'Exportar.insertar.execute';
        $_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPCSt, $sClauError, __LINE__, __FILE__);
    }
}
//************ MENUS **************
$sql_del = "DELETE FROM ref_menus WHERE id_template_menu = $id_template_menu";
if (($oDevelPCSt = $oDevelPC->query($sql_del)) === false) {
    $sClauError = 'ExportarMenu.VaciarTabla';
    $_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPCSt, $sClauError, __LINE__, __FILE__);
    $error_txt .= $sClauError;
}

$sQry = "SELECT * FROM aux_menus WHERE ok='t'";
foreach ($oDevel->query($sQry, PDO::FETCH_ASSOC) as $aDades) {
    unset($aDades['id_schema']);
    //print_r($aDades);
    $campos = "(id_menu,orden,menu,parametros,id_metamenu,menu_perm,id_grupmenu,ok,id_template_menu)";
    $valores = "(:id_menu,:orden,:menu,:parametros,:id_metamenu,:menu_perm,:id_grupmenu,:ok,$id_template_menu)";
    if (($oDevelPCSt = $oDevelPC->prepare("INSERT INTO ref_menus $campos VALUES $valores")) === false) {
        $sClauError = 'Exportar.insertar.prepare';
        $_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPCSt, $sClauError, __LINE__, __FILE__);
        $error_txt .= $sClauError;
    }

    try {
        $oDevelPCSt->execute($aDades);
    } catch (PDOException $e) {
        $error_txt .= $e->errorInfo[2];
        $sClauError = 'Exportar.insertar.execute';
        $_SESSION['oGestorErrores']->addErrorAppLastError($oDevelPCSt, $sClauError, __LINE__, __FILE__);
    }
}


ContestarJson::enviar($error_txt, 'ok');