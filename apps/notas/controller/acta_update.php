<?php

use notas\model\entity as notas;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mi_dele = core\ConfigGlobal::mi_delef();
$mi_region = core\ConfigGlobal::mi_region();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
}

$Qacta = (string)filter_input(INPUT_POST, 'acta');
$Qmod = (string)filter_input(INPUT_POST, 'mod');

if (!empty($a_sel)) { //vengo de un checkbox (caso de eliminar)
    $Qacta = urldecode(strtok($a_sel[0], "#"));
}

$dl_acta = strtok($Qacta, ' ');

if ($dl_acta == $mi_dele || $dl_acta == "?") {
    $oActa = new notas\ActaDl();
    $oActaTribunal = new notas\ActaTribunalDl();
} else {
    // Ojo si la dl ya existe no deberia hacerse
    $oActa = new notas\ActaEx();
    switch ($Qmod) {
        case 'nueva':
            $msg = _("No puede generar un acta de otra dl");
            break;
        case 'eliminar':
            $msg = _("No puede eliminar un acta de otra dl");
            break;
        case 'modificar':
            $msg = _("No puede modificar un acta de otra dl");
            break;
        default:
            $msg = _("No puede modificar un acta de otra dl");
    }
    exit($msg);
}

$Qid_asignatura = (integer)filter_input(INPUT_POST, 'id_asignatura');
$Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
$Qf_acta = (string)filter_input(INPUT_POST, 'f_acta');
$Qlibro = (integer)filter_input(INPUT_POST, 'libro');
$Qpagina = (integer)filter_input(INPUT_POST, 'pagina');
$Qlinea = (integer)filter_input(INPUT_POST, 'linea');
$Qlugar = (string)filter_input(INPUT_POST, 'lugar');
$Qobserv = (string)filter_input(INPUT_POST, 'observ');

switch ($Qmod) {
    case 'nueva':
        // Si se pone un acta ya existente, modificará los datos de ésta. Hay que avisar:
        $oActa->setActa($Qacta);
        //if (!empty($oActa->getF_acta())) { exit(_("esta acta ya existe")); }

        $oActa->setId_asignatura($Qid_asignatura);
        $oActa->setId_activ($Qid_activ);
        // la fecha debe ir antes que el acta por si hay que inventar el acta, tener la referencia de la fecha
        $oActa->setF_acta($Qf_acta);
        // comprobar valor del acta
        if (isset($Qacta)) {
            $valor = trim($Qacta);
            $reg_exp = "/^(\?|\w{1,8}\??)\s+([0-9]{0,3})\/([0-9]{2})\??$/";
            if (preg_match($reg_exp, $valor) == 1) {
            } else {
                // inventar acta.
                $valor = $oActa->inventarActa($valor, $Qf_acta);
            }
            $oActa->setActa($valor);
        }
        $oActa->setLibro($Qlibro);
        $oActa->setPagina($Qpagina);
        $oActa->setLinea($Qlinea);
        $oActa->setLugar($Qlugar);
        $oActa->setObserv($Qobserv);
        if ($oActa->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oActa->getErrorTxt();
        }
        break;
    case 'eliminar':
        $oActa->setActa($Qacta);
        $oActa->DBCarregar();

        if ($oActa->DBEliminar() === false) {
            echo _("hay un error, no se ha eliminado");
            echo "\n" . $oActa->getErrorTxt();
        }
        break;
    case 'modificar':
    default :
        $Qid_asignatura = (integer)filter_input(INPUT_POST, 'id_asignatura');
        $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
        $Qf_acta = (string)filter_input(INPUT_POST, 'f_acta');
        $Qlibro = (integer)filter_input(INPUT_POST, 'libro');
        $Qpagina = (integer)filter_input(INPUT_POST, 'pagina');
        $Qlinea = (integer)filter_input(INPUT_POST, 'linea');
        $Qlugar = (string)filter_input(INPUT_POST, 'lugar');
        $Qobserv = (string)filter_input(INPUT_POST, 'observ');

        $oActa->setActa($Qacta);
        $oActa->DBCarregar();

        $oActa->setId_asignatura($Qid_asignatura);
        //	$oActa->setId_activ($Qid_activ);
        $oActa->setF_acta($Qf_acta);
        $oActa->setLibro($Qlibro);
        $oActa->setPagina($Qpagina);
        $oActa->setLinea($Qlinea);
        $oActa->setLugar($Qlugar);
        $oActa->setObserv($Qobserv);
        if ($oActa->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oActa->getErrorTxt();
        }
        break;
}

//borrar todos (y despues poner los nuevos)
$oGesActaTribunal = new notas\GestorActaTribunalDl();
$cActaTribunal = $oGesActaTribunal->getActasTribunales(array('acta' => $Qacta));
foreach ($cActaTribunal as $oActaTribunal) {
    if ($oActaTribunal->DBEliminar() === false) {
        echo _("hay un error, no se ha eliminado");
        echo "\n" . $oActaTribunal->getErrorTxt();
    }
}

$Qexaminadores = (array)filter_input(INPUT_POST, 'examinadores', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($Qexaminadores)) {
    $i = 0;
    foreach ($Qexaminadores as $examinador) {
        $i++;
        // puede estar en blanco => no guardar.
        if (empty($examinador)) {
            continue;
        }
        $oActaTribunal = new notas\ActaTribunalDl();
        $oActaTribunal->setActa($Qacta);
        $oActaTribunal->setExaminador($examinador);
        $oActaTribunal->setOrden($i);
        if ($oActaTribunal->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oActaTribunal->getErrorTxt();
        }
    }
}
