<?php

use Ramsey\Uuid\Uuid;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\entity\Habitacion;
use src\ubiscamas\domain\value_objects\CamaDescripcion;
use src\ubiscamas\domain\value_objects\CamaId;
use src\ubiscamas\domain\value_objects\HabitacionId;
use src\ubiscamas\domain\value_objects\HabitacionNombre;
use src\ubiscamas\domain\value_objects\HabitacionOrden;
use src\ubiscamas\domain\value_objects\NumeroCamas;
use src\ubiscamas\domain\value_objects\PlantaText;
use src\ubiscamas\domain\value_objects\TipoLavabo;
use src\shared\domain\helpers\FuncTablasSupport;
$a_sel = FuncTablasSupport::inputStringList($_POST, 'sel');

$Qid_habitacion = FuncTablasSupport::inputString($_POST, 'id_habitacion');
$Qid_ubi = FuncTablasSupport::inputInt($_POST, 'id_ubi');

if ($a_sel !== []) {
    $Qid_habitacion = urldecode(strtok($a_sel[0], '#') ?: '');
}

$Qorden = FuncTablasSupport::inputInt($_POST, 'orden');
$Qnombre = FuncTablasSupport::inputString($_POST, 'nombre');
$Qnumero_camas = FuncTablasSupport::inputInt($_POST, 'numero_camas');
$Qnumero_camas_vip = FuncTablasSupport::inputInt($_POST, 'numero_camas_vip');
$Qplanta = FuncTablasSupport::inputString($_POST, 'planta');
$QtipoLavabo = FuncTablasSupport::inputInt($_POST, 'tipoLavabo');
$Qsillon = FuncTablasSupport::isTrue(FuncTablasSupport::inputString($_POST, 'sillon'));
$Qadaptada = FuncTablasSupport::isTrue(FuncTablasSupport::inputString($_POST, 'adaptada'));
$Qobservaciones = FuncTablasSupport::inputString($_POST, 'observaciones');
$Qdespacho = FuncTablasSupport::isTrue(FuncTablasSupport::inputString($_POST, 'despacho'));

/** @var HabitacionDlRepositoryInterface $habitacionRepository */
$habitacionRepository = DependencyResolver::get(HabitacionDlRepositoryInterface::class);

$error_txt = '';
try {
    if ($Qid_habitacion === '') {
        $uuid_habitacion = new HabitacionId(Uuid::uuid4()->toString());
        $oHabitacion = new Habitacion();
        $oHabitacion->setIdHabitacionVo($uuid_habitacion);
        $oHabitacion->setIdUbiVo($Qid_ubi);
    } else {
        $uuid_habitacion = HabitacionId::fromNullableString($Qid_habitacion);
        if ($uuid_habitacion === null) {
            throw new Exception(_('Habitación no válida'));
        }
        $oHabitacion = $habitacionRepository->findById($uuid_habitacion->value());
        if ($oHabitacion === null) {
            $oHabitacion = new Habitacion();
            $oHabitacion->setIdHabitacionVo($uuid_habitacion);
            $oHabitacion->setIdUbiVo($Qid_ubi);
        }
    }

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

    $habitacionRepository->Guardar($oHabitacion);

    /** @var CamaDlRepositoryInterface $camaRepository */
    $camaRepository = DependencyResolver::get(CamaDlRepositoryInterface::class);
    $a_camas_actuales = $camaRepository->getCamasByHabitacion($uuid_habitacion);
    $num_camas_actuales = count($a_camas_actuales);

    $new_camas_desc = FuncTablasSupport::inputStringList($_POST, 'new_camas_desc');
    $new_camas_larga = FuncTablasSupport::inputStringList($_POST, 'new_camas_larga');
    $new_camas_vip = FuncTablasSupport::inputStringList($_POST, 'new_camas_vip');

    foreach ($new_camas_desc as $index => $descripcion) {
        if ($descripcion !== '') {
            $oCama = new Cama();
            $oCama->setIdCamaVo(new CamaId(Uuid::uuid4()->toString()));
            $oCama->setIdHabitacionVo($uuid_habitacion);
            $oCama->setDescripcionVo(CamaDescripcion::fromNullableString($descripcion) ?? new CamaDescripcion(''));
            $oCama->setLarga(($new_camas_larga[$index] ?? '') !== '');
            $oCama->setVip(($new_camas_vip[$index] ?? '') !== '');

            $camaRepository->Guardar($oCama);
            $num_camas_actuales++;
        }
    }

    if ($Qnumero_camas > $num_camas_actuales) {
        $camas_a_crear = $Qnumero_camas - $num_camas_actuales;
        $num_camas_vip_actuales = 0;
        for ($i = 1; $i <= $camas_a_crear; $i++) {
            $oCama = new Cama();
            $oCama->setIdCamaVo(new CamaId(Uuid::uuid4()->toString()));
            $oCama->setIdHabitacionVo($uuid_habitacion);

            $desc_generada = '';
            if ($Qnombre !== '') {
                $desc_generada .= $Qnombre;
            }
            if ($camas_a_crear > 1) {
                $desc_generada .= $desc_generada === '' ? '' : ' - ';
                $desc_generada .= 'Cama ' . ($num_camas_actuales + $i);
            }

            $oCama->setDescripcionVo(CamaDescripcion::fromNullableString($desc_generada) ?? new CamaDescripcion(''));
            $oCama->setLarga(false);
            $oCama->setVip($num_camas_vip_actuales < $Qnumero_camas_vip);

            $camaRepository->Guardar($oCama);
            $num_camas_vip_actuales++;
        }
    }
} catch (Exception $e) {
    $error_txt = _("Error al guardar la habitación") . ": " . $e->getMessage();
}

ContestarJson::enviar($error_txt, 'ok');
