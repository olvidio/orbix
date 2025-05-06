<?php

// INICIO Cabecera global de URL de controlador *********************************
use web\ContestarJson;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_template_menu = (integer)filter_input(INPUT_POST, 'id_template_menu');


// Copiar de dlb a public roles-grupmenu, grupmenu, menus
$oDB = $GLOBALS['oDBE'];
$oDBPC = $GLOBALS['oDBPC'];

$error_txt = '';
$sec = 'v';

//************ GRUPMENU **************
$sql_del = 'TRUNCATE TABLE aux_grupmenu RESTART IDENTITY CASCADE';
if (($oDblSt = $oDB->query($sql_del)) === false) {
    $sClauError = 'ExportarMenu.VaciarTabla';
    $_SESSION['oGestorErrores']->addError('truncate', $sClauError, __LINE__, __FILE__);
    $error_txt .= $sClauError;
}

$sQry = "SELECT id_grupmenu, grup_menu, orden FROM ref_grupmenu WHERE id_template_menu = $Qid_template_menu ";
foreach ($oDBPC->query($sQry, PDO::FETCH_ASSOC) as $aDades) {
    $campos = "(id_grupmenu,grup_menu,orden)";
    $valores = "(:id_grupmenu,:grup_menu,:orden)";
    if (($oDblSt = $oDB->prepare("INSERT INTO aux_grupmenu $campos VALUES $valores")) === false) {
        $sClauError = 'Importar.insertar.prepare';
        $_SESSION['oGestorErrores']->addError('prepare', $sClauError, __LINE__, __FILE__);
        $error_txt .= $sClauError;
    } else {
        try {
            $oDblSt->execute($aDades);
        } catch (PDOException $e) {
            $err_txt = $e->errorInfo[2];
            $sClauError = 'Importar.insertar.execute';
            $_SESSION['oGestorErrores']->addError($err_txt, $sClauError, __LINE__, __FILE__);
            $error_txt .= $sClauError;
        }
    }
}
// Fix sequences
try {
    $sql_seq = "SELECT setval('aux_grupmenu_id_grupmenu_seq', (SELECT MAX(id_grupmenu) FROM aux_grupmenu))";
    $oDB->query($sql_seq);
} catch (PDOException $e) {
    $err_txt = $e->errorInfo[2];
    $sClauError = 'Importar.sequence.execute';
    $_SESSION['oGestorErrores']->addError($err_txt, $sClauError, __LINE__, __FILE__);
    $error_txt .= $sClauError;
}

//************ GRUPMENU_ROL**************
// En el caso de la sf, los grupmenu asociados a los roles son distintos.
// de momento no los copio. los dejo como están.
if ($sec === 'v') {
    $sql_del = 'TRUNCATE TABLE aux_grupmenu_rol RESTART IDENTITY CASCADE';
    if (($oDblSt = $oDB->query($sql_del)) === false) {
        $sClauError = 'ExportarMenu.VaciarTabla';
        $_SESSION['oGestorErrores']->addError('truncate', $sClauError, __LINE__, __FILE__);
        $error_txt .= $sClauError;
    }

    $sQry = "SELECT id_item,id_grupmenu,id_role FROM ref_grupmenu_rol WHERE id_template_menu = $Qid_template_menu ";
    foreach ($oDBPC->query($sQry, PDO::FETCH_ASSOC) as $aDades) {
        //print_r($aDades);
        $campos = "(id_item,id_grupmenu,id_role)";
        $valores = "(:id_item,:id_grupmenu,:id_role)";
        if (($oDblSt = $oDB->prepare("INSERT INTO aux_grupmenu_rol $campos VALUES $valores")) === false) {
            $sClauError = 'Importar.insertar.prepare';
            $_SESSION['oGestorErrores']->addError('prepare', $sClauError, __LINE__, __FILE__);
            $error_txt .= $sClauError;
        } else {
            try {
                $oDblSt->execute($aDades);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $sClauError = 'Importar.insertar.execute';
                $_SESSION['oGestorErrores']->addError($err_txt, $sClauError, __LINE__, __FILE__);
                $error_txt .= $sClauError;
            }
        }
    }
    // Fix sequences
    try {
        $sql_seq = "SELECT setval('aux_grupmenu_rol_id_item_seq', (SELECT MAX(id_item) FROM aux_grupmenu_rol))";
        $oDB->query($sql_seq);
    } catch (PDOException $e) {
        $err_txt = $e->errorInfo[2];
        $sClauError = 'Importar.sequence.execute';
        $_SESSION['oGestorErrores']->addError($err_txt, $sClauError, __LINE__, __FILE__);
        $error_txt .= $sClauError;
    }

}
//************ MENUS**************
$sql_del = 'TRUNCATE TABLE aux_menus RESTART IDENTITY CASCADE';
if (($oDblSt = $oDB->query($sql_del)) === false) {
    $sClauError = 'ExportarMenu.VaciarTabla';
    $_SESSION['oGestorErrores']->addError('truncate', $sClauError, __LINE__, __FILE__);
    $error_txt .= $sClauError;
}

$sQry = "SELECT id_menu,orden,menu,parametros,id_metamenu,menu_perm,id_grupmenu,ok FROM ref_menus WHERE id_template_menu = $Qid_template_menu ";
foreach ($oDBPC->query($sQry, PDO::FETCH_ASSOC) as $aDades) {
    //print_r($aDades);
    $campos = "(id_menu,orden,menu,parametros,id_metamenu,menu_perm,id_grupmenu,ok)";
    $valores = "(:id_menu,:orden,:menu,:parametros,:id_metamenu,:menu_perm,:id_grupmenu,:ok)";
    if (($oDblSt = $oDB->prepare("INSERT INTO aux_menus $campos VALUES $valores")) === false) {
        $sClauError = 'Importar.insertar.prepare';
        $_SESSION['oGestorErrores']->addError('prepare', $sClauError, __LINE__, __FILE__);
        $error_txt .= $sClauError;
    } else {
        try {
            $oDblSt->execute($aDades);
        } catch (PDOException $e) {
            $err_txt = $e->errorInfo[2];
            $sClauError = 'Importar.insertar.execute';
            $_SESSION['oGestorErrores']->addError($err_txt, $sClauError, __LINE__, __FILE__);
            $error_txt .= $sClauError;
        }
    }
}
// Fix sequences
try {
    $sql_seq = "SELECT setval('aux_menus_id_menu_seq', (SELECT MAX(id_menu) FROM aux_menus))";
    $oDB->query($sql_seq);
} catch (PDOException $e) {
    $err_txt = $e->errorInfo[2];
    $sClauError = 'Importar.sequence.execute';
    $_SESSION['oGestorErrores']->addError($err_txt, $sClauError, __LINE__, __FILE__);
    $error_txt .= $sClauError;
}



ContestarJson::enviar($error_txt, 'ok');