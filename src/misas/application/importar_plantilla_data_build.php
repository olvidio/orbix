<?php

use src\misas\application\support\MisasBuildInput;

/**
 * Funcion global que importa una plantilla a otra. Vive fuera de cualquier
 * `namespace` para que el fragmento procedural herede el espacio global y los
 * `use` que tenia el controlador legacy sigan resolviendo.
 */

use Ramsey\Uuid\Uuid as RamseyUuid;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\EncargosZona;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoDiaId;
use src\misas\domain\value_objects\EncargoDiaTend;
use src\misas\domain\value_objects\EncargoDiaTstart;
use src\misas\domain\value_objects\PlantillaConfig;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * @see \src\misas\application\ImportarPlantillaData::build()
 * @param array<string, mixed> $in
 * @return array<string, mixed>
 */
function misas_importar_plantilla_build(array $in, \src\misas\application\ImportarPlantillaData $self): array
{
    $Qid_zona = MisasBuildInput::int($in, 'id_zona');
    $QTipoPlantillaOrigen = MisasBuildInput::string($in, 'tipo_plantilla_origen');
    $QTipoPlantillaDestino = MisasBuildInput::string($in, 'tipo_plantilla_destino');

    $error_txt = '';

    try {
    $oDiaOrigen = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_UNO);
    $oDiaOrigen2 = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_DOS);
    $oDiaOrigen3 = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_TRES);
    $oFinOrigen = new DateTimeLocal(PlantillaConfig::FIN_SEMANAL_UNO);
    $oDiaDestino = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_UNO);
    $oDiaDestino2 = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_DOS);
    $oDiaDestino3 = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_TRES);
    $oFinDestino = new DateTimeLocal(PlantillaConfig::FIN_SEMANAL_UNO);
    $ndias = 7;
    $iOrigen = 0;
    $num_dia2 = '';
    $num_dia3 = '';

            $un_dia = new DateInterval('P1D');

    if (($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_SEMANAL_UNO) || ($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_SEMANAL_TRES)) {
        $oDiaOrigen = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_UNO);
    }

    if ($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_SEMANAL_TRES) {
        $oDiaOrigen2 = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_DOS);
        $oDiaOrigen3 = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_TRES);
    }

    if (($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_DOMINGOS_TRES)) {
        $oDiaOrigen = new DateTimeLocal(PlantillaConfig::INICIO_DOMINGOS_UNO);
    }

    if ($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_DOMINGOS_TRES) {
        $oDiaOrigen2 = new DateTimeLocal(PlantillaConfig::INICIO_DOMINGOS_DOS);
        $oDiaOrigen3 = new DateTimeLocal(PlantillaConfig::INICIO_DOMINGOS_TRES);
    }

    if (($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_MENSUAL_TRES)) {
        $oDiaOrigen = new DateTimeLocal(PlantillaConfig::INICIO_MENSUAL_UNO);
        $oFinOrigen = new DateTimeLocal(PlantillaConfig::FIN_MENSUAL_UNO);
    }

    if ($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_MENSUAL_TRES) {
        $oDiaOrigen2 = new DateTimeLocal(PlantillaConfig::INICIO_MENSUAL_DOS);
        $oDiaOrigen3 = new DateTimeLocal(PlantillaConfig::INICIO_MENSUAL_TRES);
    }

    if (($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_SEMANAL_UNO) || ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_SEMANAL_TRES)) {
        $oDiaDestino = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_UNO);
        $oFinDestino = new DateTimeLocal(PlantillaConfig::FIN_SEMANAL_UNO);
        $ndias = 7;
    }

    if ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_SEMANAL_TRES) {
        $oDiaDestino2 = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_DOS);
        $oDiaDestino3 = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_TRES);
        $oFinDestino = new DateTimeLocal(PlantillaConfig::FIN_SEMANAL_TRES);
    }

    if (($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_DOMINGOS_TRES)) {
        $oDiaDestino = new DateTimeLocal(PlantillaConfig::INICIO_DOMINGOS_UNO);
        $oFinDestino = new DateTimeLocal(PlantillaConfig::FIN_DOMINGOS_UNO);
        $ndias = 11;
    }

    if ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_DOMINGOS_TRES) {
        $oDiaDestino2 = new DateTimeLocal(PlantillaConfig::INICIO_DOMINGOS_DOS);
        $oDiaDestino3 = new DateTimeLocal(PlantillaConfig::INICIO_DOMINGOS_TRES);
        $oFinDestino = new DateTimeLocal(PlantillaConfig::FIN_DOMINGOS_TRES);
    }

    if (($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_MENSUAL_TRES)) {
        $oDiaDestino = new DateTimeLocal(PlantillaConfig::INICIO_MENSUAL_UNO);
        $oFinDestino = new DateTimeLocal(PlantillaConfig::FIN_MENSUAL_UNO);
        $ndias = 35;
    }

    if ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_MENSUAL_TRES) {
        $oDiaDestino2 = new DateTimeLocal(PlantillaConfig::INICIO_MENSUAL_DOS);
        $oDiaDestino3 = new DateTimeLocal(PlantillaConfig::INICIO_MENSUAL_TRES);
        $oFinDestino = new DateTimeLocal(PlantillaConfig::FIN_MENSUAL_TRES);
    }

    $EncargoTipoRepository = $self->getEncargoTipoRepository();

    $grupo = '8...';
    $aWhere = [];
    $aOperador = [];
    $aWhere['id_tipo_enc'] = '^' . $grupo;
    $aOperador['id_tipo_enc'] = '~';
    $cEncargoTipos = $EncargoTipoRepository->getEncargoTipos($aWhere, $aOperador);

    $a_tipo_enc = [];
    foreach ($cEncargoTipos as $oEncargoTipo) {
        if ($oEncargoTipo->getId_tipo_enc() >= 8100) {
            $a_tipo_enc[] = $oEncargoTipo->getId_tipo_enc();
        }
    }

    $sInicio_iso = $oDiaDestino->getIso();
    $sFin_iso = $oFinDestino->getIso();
    $orden = 'prioridad';

    $EncargosZona = new EncargosZona($Qid_zona, $oDiaDestino, $oFinDestino, $self->getEncargoHorarioRepository(), $self->getEncargoRepository());
    $EncargosZona->setATipoEnc($a_tipo_enc);
    $cEncargosZona = $EncargosZona->getEncargos();
    $EncargoDiaRepository = $self->getEncargoDiaRepository();
    foreach ($cEncargosZona as $oEncargo) {
        $id_enc = $oEncargo->getId_enc();

        $aWhere = [
            'id_enc' => $id_enc,
            'tstart' => "'$sInicio_iso', '$sFin_iso'",
        ];
        $aOperador = [
            'tstart' => 'BETWEEN',
        ];

        // Borro los encargos de la zona ya asignados en ese periodo
        $cEncargosaBorrar = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);
        foreach ($cEncargosaBorrar as $oEncargoaBorrar) {
            $EncargoDiaRepository->Eliminar($oEncargoaBorrar);
        }
    }

    for ($i = 0; $i < $ndias; $i++) {
        $num_dia = $oDiaDestino->format('d-m-Y');
        if (($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_MENSUAL_TRES)) {
            $num_dia2 = $oDiaDestino2->format('d-m-Y');
            $num_dia3 = $oDiaDestino3->format('d-m-Y');
        }
        if (($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_SEMANAL_UNO) || ($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_SEMANAL_TRES)) {
            $oDiaOrigen = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_UNO);
            $oDiaOrigen2 = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_UNO);
            $oDiaOrigen3 = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_UNO);
            $iOrigen = $i;
            // como se empieza el lunes, lunes+6 es domingo
            if ((($i > 6) && ($i < 11)) && (($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_DOMINGOS_TRES))) {
                $iOrigen = 6;
            }
            if (($i > 6) && (($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_MENSUAL_TRES))) {
                $iOrigen = $i % 7;
            }
            $iOrigen2 = $iOrigen;
            $iOrigen3 = $iOrigen;
        }
        if ($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_SEMANAL_TRES) {
            $oDiaOrigen2 = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_DOS);
            $oDiaOrigen3 = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_TRES);
        }

        if (($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_DOMINGOS_TRES)) {
            $oDiaOrigen = new DateTimeLocal(PlantillaConfig::INICIO_DOMINGOS_UNO);
            $iOrigen = $i;
            // como se empieza el lunes, lunes+6 es domingo
            if (($i > 6) && (($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_MENSUAL_TRES))) {
                $iOrigen = $i % 7;
                if ($iOrigen === 6) {
                    $iOrigen += intdiv($i, 7);
                }
            }
        }
        if ($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_DOMINGOS_TRES) {
            $oDiaOrigen2 = new DateTimeLocal(PlantillaConfig::INICIO_DOMINGOS_DOS);
            $oDiaOrigen3 = new DateTimeLocal(PlantillaConfig::INICIO_DOMINGOS_TRES);
        }

        if (($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_MENSUAL_TRES)) {
            $oDiaOrigen = new DateTimeLocal(PlantillaConfig::INICIO_MENSUAL_UNO);
            $iOrigen = $i;
            if ((($i > 6) && ($i < 11)) && (($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_DOMINGOS_TRES))) {
                $iOrigen = 6 + ($i - 6) * 7;
            }
        }

        if ($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_MENSUAL_TRES) {
            $oDiaOrigen2 = new DateTimeLocal(PlantillaConfig::INICIO_MENSUAL_DOS);
            $oDiaOrigen3 = new DateTimeLocal(PlantillaConfig::INICIO_MENSUAL_TRES);
        }

        $oDiaOrigen->add(new DateInterval("P{$iOrigen}D"));
        if (($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantillaOrigen === PlantillaConfig::PLANTILLA_MENSUAL_TRES)) {
            $oDiaOrigen2->add(new DateInterval("P{$iOrigen}D"));
            $oDiaOrigen3->add(new DateInterval("P{$iOrigen}D"));
        }


        $EncargoDiaRepository = $self->getEncargoDiaRepository();
        foreach ($cEncargosZona as $oEncargo) {
            $inicio_dia_plantilla = $oDiaOrigen->format('Y-m-d') . ' 00:00:00';
            $fin_dia_plantilla = $oDiaOrigen->format('Y-m-d') . ' 23:59:59';
            $id_enc = $oEncargo->getId_enc();

            $aWhere = [
                'id_enc' => $id_enc,
                'tstart' => "'$inicio_dia_plantilla', '$fin_dia_plantilla'",
            ];
            $aOperador = [
                'tstart' => 'BETWEEN',
            ];
            $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);
            if (count($cEncargosDia) > 1) {
                throw new \RuntimeException(_("solo deberia haber uno"));
            }
            if (count($cEncargosDia) === 1) {
                $oEncargoDia = $cEncargosDia[0];
                $id_nom = $oEncargoDia->getId_nom();
                $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                $hora_fin = $oEncargoDia->getTend()->format('H:i');
                $observ = $oEncargoDia->getObserv();

                $oEncargoDia = new EncargoDia();
                $Uuid = new EncargoDiaId(RamseyUuid::uuid4()->toString());
                $oEncargoDia->setUuid_item($Uuid);
                $oEncargoDia->setId_nom($id_nom);
                $tstart = new EncargoDiaTstart($num_dia, $hora_ini);
                $oEncargoDia->setTstart($tstart);

                $tend = new EncargoDiaTend($num_dia, $hora_fin);
                $oEncargoDia->setTend($tend);

                if (isset($observ)) {
                    $oEncargoDia->setObserv($observ);
                }
                $oEncargoDia->setId_enc($id_enc);
                if ($EncargoDiaRepository->Guardar($oEncargoDia) === false) {
                    $error_txt .= $EncargoDiaRepository->getErrorTxt();
                }
            }
            if (($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_MENSUAL_TRES)) {
                $inicio_dia_plantilla = $oDiaOrigen2->format('Y-m-d') . ' 00:00:00';
                $fin_dia_plantilla = $oDiaOrigen2->format('Y-m-d') . ' 23:59:59';

                $aWhere = [
                    'id_enc' => $id_enc,
                    'tstart' => "'$inicio_dia_plantilla', '$fin_dia_plantilla'",
                ];
                $aOperador = [
                    'tstart' => 'BETWEEN',
                ];
                $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);
                if (count($cEncargosDia) > 1) {
                    throw new \RuntimeException(_("solo deberia haber uno"));
                }
                if (count($cEncargosDia) === 1) {
                    $oEncargoDia = $cEncargosDia[0];
                    $id_nom = $oEncargoDia->getId_nom();
                    $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                    $hora_fin = $oEncargoDia->getTend()->format('H:i');
                    $observ = $oEncargoDia->getObserv();

                    $oEncargoDia = new EncargoDia();
                    $Uuid = new EncargoDiaId(RamseyUuid::uuid4()->toString());
                    $oEncargoDia->setUuid_item($Uuid);
                    $oEncargoDia->setId_nom($id_nom);
                    $tstart = new EncargoDiaTstart($num_dia2, $hora_ini);
                    $oEncargoDia->setTstart($tstart);

                    $tend = new EncargoDiaTend($num_dia2, $hora_fin);
                    $oEncargoDia->setTend($tend);

                    if (isset($observ)) {
                        $oEncargoDia->setObserv($observ);
                    }
                    $oEncargoDia->setId_enc($id_enc);
                    if ($EncargoDiaRepository->Guardar($oEncargoDia) === false) {
                        $error_txt .= $EncargoDiaRepository->getErrorTxt();
                    }
                }

                $inicio_dia_plantilla = $oDiaOrigen3->format('Y-m-d') . ' 00:00:00';
                $fin_dia_plantilla = $oDiaOrigen3->format('Y-m-d') . ' 23:59:59';

                $aWhere = [
                    'id_enc' => $id_enc,
                    'tstart' => "'$inicio_dia_plantilla', '$fin_dia_plantilla'",
                ];
                $aOperador = [
                    'tstart' => 'BETWEEN',
                ];
                $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);
                if (count($cEncargosDia) > 1) {
                    throw new \RuntimeException(_("solo deberia haber uno"));
                }
                if (count($cEncargosDia) === 1) {
                    $oEncargoDia = $cEncargosDia[0];
                    $id_nom = $oEncargoDia->getId_nom();
                    $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                    $hora_fin = $oEncargoDia->getTend()->format('H:i');
                    $observ = $oEncargoDia->getObserv();

                    $oEncargoDia = new EncargoDia();
                    $Uuid = new EncargoDiaId(RamseyUuid::uuid4()->toString());
                    $oEncargoDia->setUuid_item($Uuid);
                    $oEncargoDia->setId_nom($id_nom);
                    $tstart = new EncargoDiaTstart($num_dia3, $hora_ini);
                    $oEncargoDia->setTstart($tstart);

                    $tend = new EncargoDiaTend($num_dia3, $hora_fin);
                    $oEncargoDia->setTend($tend);

                    if (isset($observ)) {
                        $oEncargoDia->setObserv($observ);
                    }
                    $oEncargoDia->setId_enc($id_enc);
                    if ($EncargoDiaRepository->Guardar($oEncargoDia) === false) {
                        $error_txt .= $EncargoDiaRepository->getErrorTxt();
                    }
                }
            }
        }

        $oDiaDestino->add(new DateInterval('P1D'));
        if (($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantillaDestino === PlantillaConfig::PLANTILLA_MENSUAL_TRES)) {
            $oDiaDestino2->add(new DateInterval('P1D'));
            $oDiaDestino3->add(new DateInterval('P1D'));
        }
    }
    } catch (\RuntimeException $e) {
        return [
            'error' => $e->getMessage(),
            'success' => false,
        ];
    }

    return [
        'error' => $error_txt,
        'success' => $error_txt === '',
    ];
}
