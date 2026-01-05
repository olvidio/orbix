<?php

use core\ConfigGlobal;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\entity\ActaTribunal;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mi_dele = ConfigGlobal::mi_delef();
$mi_region = ConfigGlobal::mi_region();

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

if ($dl_acta != $mi_dele && $dl_acta !== "?") {
    // Ojo si la dl ya existe no debería hacerse
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

$oActa = new Acta();
$ActaDlRepository = $GLOBALS['container']->get(ActaDlRepositoryInterface::class);
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
            $valor = Acta::inventarActa($Qacta, $Qf_acta);
            $oActa->setActa($valor);
        }
        $oActa->setLibro($Qlibro);
        $oActa->setPagina($Qpagina);
        $oActa->setLinea($Qlinea);
        $oActa->setLugar($Qlugar);
        $oActa->setObserv($Qobserv);
        if ($ActaDlRepository->Guardar($oActa) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $ActaDlRepository->getErrorTxt();
        }
        break;
    case 'eliminar':
        $oActa = $ActaDlRepository->findById($Qacta);

        if ($ActaDlRepository->Eliminar($oActa) === false) {
            echo _("hay un error, no se ha eliminado");
            echo "\n" . $ActaDlRepository->getErrorTxt();
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

        $oActa = $ActaDlRepository->findById($Qacta);

        $oActa->setId_asignatura($Qid_asignatura);
        //	$oActa->setId_activ($Qid_activ);
        $oActa->setF_acta($Qf_acta);
        $oActa->setLibro($Qlibro);
        $oActa->setPagina($Qpagina);
        $oActa->setLinea($Qlinea);
        $oActa->setLugar($Qlugar);
        $oActa->setObserv($Qobserv);
        if ($ActaDlRepository->Guardar($oActa) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $ActaDlRepository->getErrorTxt();
        }
        break;
}

//borrar todos (y después poner los nuevos)
$ActaTribunalDlRepository = $GLOBALS['container']->get(ActaTribunalDlRepositoryInterface::class);
$cActaTribunal = $ActaTribunalDlRepository->getActasTribunales(['acta' => $Qacta]);
foreach ($cActaTribunal as $oActaTribunal) {
    if ($ActaTribunalDlRepository->Eliminar($oActaTribunal) === false) {
        echo _("hay un error, no se ha eliminado");
        echo "\n" . $ActaTribunalDlRepository->getErrorTxt();
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
        $newIdItem = $ActaTribunalDlRepository->getNewId();
        $oActaTribunal = new ActaTribunal();
        $oActaTribunal->setId_item($newIdItem);
        $oActaTribunal->setActa($Qacta);
        $oActaTribunal->setExaminador($examinador);
        $oActaTribunal->setOrden($i);
        if ($ActaTribunalDlRepository->Guardar($oActaTribunal) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $ActaTribunalDlRepository->getErrorTxt();
        }
    }
}
