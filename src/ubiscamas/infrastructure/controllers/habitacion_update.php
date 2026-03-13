<?php
// INICIO Cabecera global de URL de controlador *********************************
use Ramsey\Uuid\Uuid;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\entity\Habitacion;
use src\ubiscamas\domain\value_objects\TipoLavabo;
use src\ubiscamas\domain\value_objects\HabitacionNombre;
use src\ubiscamas\domain\value_objects\HabitacionOrden;
use src\ubiscamas\domain\value_objects\NumeroCamas;
use src\ubiscamas\domain\value_objects\PlantaText;
use web\ContestarJson;
use function core\is_true;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
}

$Qnuevo = (string)filter_input(INPUT_POST, 'nuevo');
$Qid_habitacion = (string)filter_input(INPUT_POST, 'id_habitacion');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

if (!empty($a_sel)) { //vengo de un checkbox (caso de eliminar)
    $Qid_habitacion = urldecode(strtok($a_sel[0], "#"));
}

$Qorden = (integer)filter_input(INPUT_POST, 'orden');
$Qnombre = (string)filter_input(INPUT_POST, 'nombre');
$Qnumero_camas = (integer)filter_input(INPUT_POST, 'numero_camas', FILTER_VALIDATE_INT);
$Qnumero_camas_vip = (integer)filter_input(INPUT_POST, 'numero_camas_vip', FILTER_VALIDATE_INT);
$Qplanta = (string)filter_input(INPUT_POST, 'planta');
$QtipoLavabo = (integer)filter_input(INPUT_POST, 'tipoLavabo', FILTER_VALIDATE_INT);
$Qsillon = is_true(filter_input(INPUT_POST, 'sillon'));
$Qadaptada = is_true(filter_input(INPUT_POST, 'adaptada'));
$Qfumador = is_true(filter_input(INPUT_POST, 'fumador'));
$Qdespacho = is_true(filter_input(INPUT_POST, 'despacho'));

$HabitacionRepository = $GLOBALS['container']->get(HabitacionDlRepositoryInterface::class);

$error_txt = '';
try {
    if (!empty($Qorden)) {
        $newId = Uuid::uuid4()->toString();
        $oHabitacion = new Habitacion();
        $oHabitacion->setIdHabitacionVo($newId);
        $oHabitacion->setIdUbiVo($Qid_ubi);
    } else {
        $oHabitacion = $HabitacionRepository->findById($Qid_habitacion);
    }
    if (!empty($oHabitacion)) {
        $oHabitacion->setOrdenVo(new HabitacionOrden($Qorden));
        $oHabitacion->setNombreVo(HabitacionNombre::fromNullableString($Qnombre));
        $oHabitacion->setNumeroCamasVo(NumeroCamas::fromNullableInt($Qnumero_camas));
        $oHabitacion->setNumeroCamasVipVo(NumeroCamas::fromNullableInt($Qnumero_camas_vip));
        $oHabitacion->setPlantaVo(PlantaText::fromNullableString($Qplanta));
        $oHabitacion->settipoLavaboVo(TipoLavabo::fromNullableInt($QtipoLavabo));
        $oHabitacion->setSillon($Qsillon);
        $oHabitacion->setAdaptada($Qadaptada);
        $oHabitacion->setFumador($Qfumador);
        $oHabitacion->setDespacho($Qdespacho);

        $HabitacionRepository->Guardar($oHabitacion);
    }
} catch (Exception $e) {
    $error_txt = _("Error al guardar la habitación") . ": " . $e->getMessage();
}

ContestarJson::enviar($error_txt, 'ok');