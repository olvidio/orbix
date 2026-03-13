<?php

use frontend\shared\model\ViewNewPhtml;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\value_objects\TipoLavabo;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qnuevo = (string)filter_input(INPUT_POST, 'nuevo');
$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');

$oPosicion->recordar($Qrefresh);

$Qid_habitacion = '';
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$orden = '';
$nombre = '';
$numero_camas = '';
$numero_camas_vip = '';
$planta = '';
$sillon = false;
$adaptada = false;
$fumador = false;
$despacho = false;
$tipoLavabo = null;
$a_camas = [];

if (empty($Qnuevo)) {
    $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!empty($a_sel)) { //vengo de un checkbox
        $Qid_habitacion = strtok($a_sel[0], "#");
        // el scroll id es de la página anterior, hay que guardarlo allí
        $oPosicion->addParametro('id_sel', $a_sel, 1);
        $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
        $oPosicion->addParametro('scroll_id', $scroll_id, 1);
    } else {
        $Qid_habitacion = (string)filter_input(INPUT_POST, 'id_habitacion');
    }

    // Sobre-escribe el scroll_id que se pueda tener
    if (isset($_POST['stack'])) {
        $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    } else {
        $stack = '';
    }

    //Si vengo por medio de Posicion, borro la última
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }

    $HabitacionRepository = $GLOBALS['container']->get(HabitacionDlRepositoryInterface::class);
    $oHabitacion = $HabitacionRepository->findById($Qid_habitacion);
    if (!empty($oHabitacion)) {
        $Qid_ubi = $oHabitacion->getIdUbiVo();
        $orden = $oHabitacion->getOrdenVo()?->value() ?? 0;
        $nombre = $oHabitacion->getNombreVo()?->value() ?? '';
        $numero_camas = $oHabitacion->getNumeroCamasVo()?->value() ?? null;
        $numero_camas_vip = $oHabitacion->getNumeroCamasVipVo()?->value() ?? null;
        $planta = $oHabitacion->getPlantaVo()?->value() ?? '';
        $sillon = $oHabitacion->isSillon() ?? false;
        $adaptada = $oHabitacion->isAdaptada() ?? false;
        $fumador = $oHabitacion->isFumador() ?? false;
        $despacho = $oHabitacion->isDespacho() ?? false;
        $tipoLavabo = $oHabitacion->getTipoLavaboVo()?->value();

        // Obtener las camas de esta habitación
        $CamaRepository = $GLOBALS['container']->get(CamaDlRepositoryInterface::class);
        $a_camas = $CamaRepository->getCamasByHabitacion($Qid_habitacion);
    }
}

// Array de tipos de tipoLavabo
$a_tipos_tipoLavabo = TipoLavabo::getArrayTipoLavabo();

$oHash = new Hash();
$camposForm = 'orden!nombre!numero_camas!numero_camas_vip!planta!sillon!adaptada!fumador!despacho!tipoLavabo';
$camposChk = 'sillon!adaptada!fumador!despacho';

$oHash->setCamposForm($camposForm);
$oHash->setCamposChk($camposChk);
$a_camposHidden = array(
    'id_habitacion' => $Qid_habitacion,
    'id_ubi' => $Qid_ubi,
    'nuevo' => $Qnuevo,
);
$oHash->setArraycamposHidden($a_camposHidden);

$oHashActualizar = new Hash();
$oHashActualizar->setCamposNo('refresh');
$a_camposHiddenActualizar = array(
    'id_habitacion' => $Qid_habitacion,
    'id_ubi' => $Qid_ubi,
);
$oHashActualizar->setArraycamposHidden($a_camposHiddenActualizar);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashActualizar' => $oHashActualizar,
    'oHash' => $oHash,
    'id_habitacion' => $Qid_habitacion,
    'id_ubi' => $Qid_ubi,
    'orden' => $orden,
    'nombre' => $nombre,
    'numero_camas' => $numero_camas,
    'numero_camas_vip' => $numero_camas_vip,
    'planta' => $planta,
    'sillon' => $sillon,
    'adaptada' => $adaptada,
    'fumador' => $fumador,
    'despacho' => $despacho,
    'tipoLavabo' => $tipoLavabo,
    'a_tipos_tipoLavabo' => $a_tipos_tipoLavabo,
    'a_camas' => $a_camas,
];

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('habitacion_form.phtml', $a_campos);
