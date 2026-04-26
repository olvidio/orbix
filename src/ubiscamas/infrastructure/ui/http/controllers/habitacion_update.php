<?php

use Ramsey\Uuid\Uuid;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\entity\Habitacion;
use src\ubiscamas\domain\value_objects\CamaDescripcion;
use src\ubiscamas\domain\value_objects\HabitacionId;
use src\ubiscamas\domain\value_objects\TipoLavabo;
use src\ubiscamas\domain\value_objects\HabitacionNombre;
use src\ubiscamas\domain\value_objects\HabitacionOrden;
use src\ubiscamas\domain\value_objects\NumeroCamas;
use src\ubiscamas\domain\value_objects\PlantaText;
use frontend\shared\web\ContestarJson;
use function src\shared\domain\helpers\is_true;

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

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
$Qobservaciones = (string)filter_input(INPUT_POST, 'observaciones');
$Qdespacho = is_true(filter_input(INPUT_POST, 'despacho'));

$HabitacionRepository = $GLOBALS['container']->get(HabitacionDlRepositoryInterface::class);

$error_txt = '';
try {
    if (empty($Qid_habitacion)) {
        $uuid_habitacion = HabitacionId::fromNullableString(Uuid::uuid4()->toString());
        $oHabitacion = new Habitacion();
        $oHabitacion->setIdHabitacionVo($uuid_habitacion);
        $oHabitacion->setIdUbiVo($Qid_ubi);
    } else {
        $uuid_habitacion = HabitacionId::fromNullableString($Qid_habitacion);
        $oHabitacion = $HabitacionRepository->findById($uuid_habitacion);
        if ($oHabitacion === null) {
            // It could be a new room with a pre-generated UUID from the frontend
            $oHabitacion = new Habitacion();
            $oHabitacion->setIdHabitacionVo($uuid_habitacion);
            $oHabitacion->setIdUbiVo($Qid_ubi);
        }
    }

    if ($oHabitacion !== null) {
        $oHabitacion->setOrdenVo(new HabitacionOrden($Qorden));
        $oHabitacion->setNombreVo(HabitacionNombre::fromNullableString($Qnombre));
        $oHabitacion->setNumeroCamasVo(NumeroCamas::fromNullableInt($Qnumero_camas));
        $oHabitacion->setNumeroCamasVipVo(NumeroCamas::fromNullableInt($Qnumero_camas_vip));
        $oHabitacion->setPlantaVo(PlantaText::fromNullableString($Qplanta));
        $oHabitacion->settipoLavaboVo(TipoLavabo::fromNullableInt($QtipoLavabo));
        $oHabitacion->setSillon($Qsillon);
        $oHabitacion->setAdaptada($Qadaptada);
        $oHabitacion->setObservacionesVo($Qobservaciones);
        $oHabitacion->setDespacho($Qdespacho);

        $HabitacionRepository->Guardar($oHabitacion);

        // --- Cama Handling ---
        $CamaRepository = $GLOBALS['container']->get(CamaDlRepositoryInterface::class);
        $a_camas_actuales = $CamaRepository->getCamasByHabitacion($uuid_habitacion);
        if ($a_camas_actuales === false) {
            $a_camas_actuales = [];
        }
        $num_camas_actuales = count($a_camas_actuales);

        // 1. Handle manually added beds from the frontend UI
        $new_camas_desc = (array)filter_input(INPUT_POST, 'new_camas_desc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $new_camas_larga = (array)filter_input(INPUT_POST, 'new_camas_larga', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $new_camas_vip = (array)filter_input(INPUT_POST, 'new_camas_vip', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        foreach ($new_camas_desc as $index => $descripcion) {
            if (!empty($descripcion)) {
                $newCamaId = Uuid::uuid4()->toString();
                $oCama = new Cama();
                $oCama->setIdCamaVo($newCamaId);
                $oCama->setIdHabitacionVo($uuid_habitacion);
                $oCama->setDescripcionVo(CamaDescripcion::fromNullableString($descripcion));
                $oCama->setLarga(!empty($new_camas_larga[$index]));
                $oCama->setVip(!empty($new_camas_vip[$index]));
                
                $CamaRepository->Guardar($oCama);
                $num_camas_actuales++;
            }
        }

        // 2. Auto-generate beds to match Qnumero_camas
        if ($Qnumero_camas > $num_camas_actuales) {
            $camas_a_crear = $Qnumero_camas - $num_camas_actuales;
            $num_camas_vip_actuales = 0;
            for ($i = 1; $i <= $camas_a_crear; $i++) {
                $newCamaId = Uuid::uuid4()->toString();
                $oCama = new Cama();
                $oCama->setIdCamaVo($newCamaId);
                $oCama->setIdHabitacionVo($uuid_habitacion);

                // si el número de camas es = 1, no hace falta poner 'cama'
                $desc_generada = '';
                if (!empty($Qnombre)) {
                    $desc_generada .= $Qnombre;
                }
                if ($camas_a_crear > 1) {
                    $desc_generada .= empty($desc_generada)? '' : " - ";
                    $desc_generada .= "Cama " . ($num_camas_actuales + $i);
                }

                $oCama->setDescripcionVo(CamaDescripcion::fromNullableString($desc_generada));
                $oCama->setLarga(false);
                $vip = ($num_camas_vip_actuales < $Qnumero_camas_vip);
                $oCama->setVip($vip);
                
                $CamaRepository->Guardar($oCama);
                $num_camas_vip_actuales++;
            }
        }
    }
} catch (Exception $e) {
    $error_txt = _("Error al guardar la habitación") . ": " . $e->getMessage();
}

ContestarJson::enviar($error_txt, 'ok');