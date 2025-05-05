<?php


// INICIO Cabecera global de URL de controlador *********************************
use actividadcargos\model\entity\GestorActividadCargo;
use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividad;
use core\ViewTwig;
use encargossacd\model\EncargoConstants;
use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\GestorEncargoSacdHorario;
use encargossacd\model\entity\GestorEncargoTipo;
use misas\domain\EncargoDiaId;
use misas\domain\EncargoDiaTend;
use misas\domain\EncargoDiaTstart;
use misas\domain\entity\EncargoDia;
use misas\domain\entity\InicialesSacd;
use misas\domain\repositories\EncargoDiaRepository;
use misas\model\EncargosZona;
use web\DateTimeLocal;
use web\Hash;
use web\TiposActividades;
use Ramsey\Uuid\Uuid as RamseyUuid;
use zonassacd\model\entity\GestorZonaSacd;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$QTipoPlantillaOrigen = (string)filter_input(INPUT_POST, 'tipo_plantilla_origen');
$QTipoPlantillaDestino = (string)filter_input(INPUT_POST, 'tipo_plantilla_destino');

$un_dia = new DateInterval('P1D');

echo 'zona:'.$Qid_zona.' tipo plantilla origen: '.$QTipoPlantillaOrigen.' tipo plantilla destino '.$QTipoPlantillaDestino.'<br>';

$a_dias_semana_breve=[1=>'L', 2=>'M', 3=>'X', 4=>'J', 5=>'V', 6=>'S', 7=>'D'];
$a_nombre_mes_breve=[1=>'Ene', 2=>'feb', 3=>'mar', 4=>'abr', 5=>'may', 6=>'jun', 7=>'jul', 8=>'ago', 9=>'sep', 10=>'oct', 11=>'nov', 12=>'dic'];


if(($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_SEMANAL_UNO) || ($QTipoPlantillaOrigen== EncargoDia::PLANTILLA_SEMANAL_TRES)) {
    $oDiaOrigen = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_UNO);
}

if($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_SEMANAL_TRES) {
    $oDiaOrigen2 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_DOS);
    $oDiaOrigen3 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_TRES);
}

if(($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_DOMINGOS_TRES)) {
    $oDiaOrigen = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_UNO);
}

if($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_DOMINGOS_TRES) {
    $oDiaOrigen2 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_DOS);
    $oDiaOrigen3 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_TRES);
}

if(($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_MENSUAL_TRES)) {
    $oDiaOrigen = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaOrigenS2 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaOrigenS2->add(new DateInterval('P7D'));
    $oDiaOrigenS3 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaOrigenS3->add(new DateInterval('P14D'));
    $oDiaOrigenS4 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaOrigenS4->add(new DateInterval('P21D'));
    $oDiaOrigenS5 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaOrigenS5->add(new DateInterval('P28D'));
    $oFinOrigen = new DateTimeLocal(EncargoDia::FIN_MENSUAL_UNO);
}

if($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_MENSUAL_TRES) {
    $oDiaOrigen2 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_DOS);
    $oDiaOrigen2S2 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaOrigen2S2->add(new DateInterval('P7D'));
    $oDiaOrigen2S3 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaOrigen2S3->add(new DateInterval('P14D'));
    $oDiaOrigen2S4 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaOrigen2S4->add(new DateInterval('P21D'));
    $oDiaOrigen2S5 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaOrigen2S5->add(new DateInterval('P28D'));
    $oFinOrigen2 = new DateTimeLocal(EncargoDia::FIN_MENSUAL_DOS);
    $oDiaOrigen3 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_TRES);
    $oDiaOrigen3S2 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaOrigen3S2->add(new DateInterval('P7D'));
    $oDiaOrigen3S3 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaOrigen3S3->add(new DateInterval('P14D'));
    $oDiaOrigen3S4 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaOrigen3S4->add(new DateInterval('P21D'));
    $oDiaOrigen3S5 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaOrigen3S5->add(new DateInterval('P28D'));
    $oFinOrigen3 = new DateTimeLocal(EncargoDia::FIN_MENSUAL_TRES);
}

if(($QTipoPlantillaDestino== EncargoDia::PLANTILLA_SEMANAL_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_SEMANAL_TRES)) {
    $oDiaDestino = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_UNO);
}

if($QTipoPlantillaDestino== EncargoDia::PLANTILLA_SEMANAL_TRES) {
    $oDiaDestino2 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_DOS);
    $oInicioDestino3 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_TRES);
}

if(($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_TRES)) {
    $oDiaDestino = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_UNO);
}

if($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_TRES) {
    $oDiaDestino2 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_DOS);
    $oDiaDestino3 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_TRES);
}

if(($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_TRES)) {
    $oDiaDestino = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestinoS2 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestinoS2->add(new DateInterval('P7D'));
    $oDiaDestinoS3 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestinoS3->add(new DateInterval('P14D'));
    $oDiaDestinoS4 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestinoS4->add(new DateInterval('P21D'));
    $oDiaDestinoS5 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestinoS5->add(new DateInterval('P28D'));
    $oFinDestino = new DateTimeLocal(EncargoDia::FIN_MENSUAL_UNO);
}

if($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_TRES) {
    $oDiaDestino2 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestino2S2 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestino2S2->add(new DateInterval('P7D'));
    $oDiaDestino2S3 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestino2S3->add(new DateInterval('P14D'));
    $oDiaDestino2S4 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestino2S4->add(new DateInterval('P21D'));
    $oDiaDestino2S5 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestino2S5->add(new DateInterval('P28D'));
    $oFinDestino2 = new DateTimeLocal(EncargoDia::FIN_MENSUAL_DOS);
    $oDiaDestino3 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestino3S2 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestino3S2->add(new DateInterval('P7D'));
    $oDiaDestino3S3 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestino3S3->add(new DateInterval('P14D'));
    $oDiaDestino3S4 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestino3S4->add(new DateInterval('P21D'));
    $oDiaDestino3S5 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestino3S5->add(new DateInterval('P28D'));
    $oFinDestino3 = new DateTimeLocal(EncargoDia::FIN_MENSUAL_TRES);
}

$intervalo_dia = new DateInterval('P1D');
$intervalo_semana = new DateInterval('P7D');

for ($i=1;$i<=7;$i++) {
    echo 'destino: '.$oDiaDestino->format('d-m-Y').'<---';
    echo 'origen: '.$oDiaOrigen->format('d-m-Y').'<br>';
    if (($i==7) && ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_TRES)) {
        for ($j=1;$j<5;$j++) {
            $oDiaDestino->add($intervalo_dia);
            if(($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_DOMINGOS_TRES)) {
                $oDiaOrigen->add($intervalo_dia);
            }
            if(($QTipoPlantillaOrigen== EncargoDia::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaOrigen== EncargoDia::PLANTILLA_MENSUAL_TRES)) {
                $oDiaOrigen->add($intervalo_semana);
            }
            echo 'destino: '.$oDiaDestino->format('d-m-Y').'<---';
            echo 'origen: '.$oDiaOrigen->format('d-m-Y').'<br>';
        }
    }
    if (($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_TRES)) {
//        echo 'destino: '.$oDiaDestino->format('d-m-Y').'<---';
//        echo 'origen: '.$oDiaOrigen->format('d-m-Y').'<br>';
        if(($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_SEMANAL_UNO) || ($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_SEMANAL_TRES)) {
            echo 'destinos: '.$oDiaDestinoS2->format('d-m-Y').'<---';
            echo 'origen: '.$oDiaOrigen->format('d-m-Y').'<br>';
            echo 'destino: '.$oDiaDestinoS3->format('d-m-Y').'<---';
            echo 'origen: '.$oDiaOrigen->format('d-m-Y').'<br>';
            echo 'destino: '.$oDiaDestinoS4->format('d-m-Y').'<---';
            echo 'origen: '.$oDiaOrigen->format('d-m-Y').'<br>';
            echo 'destino: '.$oDiaDestinoS5->format('d-m-Y').'<---';
            echo 'origen: '.$oDiaOrigen->format('d-m-Y').'<br>';
        }
        if(($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_DOMINGOS_TRES)) {
            if ($i==7) {
                $oDiaOrigen->add($intervalo_dia);
            }
            echo 'destinod: '.$oDiaDestinoS2->format('d-m-Y').'<---';
            echo 'origen: '.$oDiaOrigen->format('d-m-Y').'<br>';
            if ($i==7) {
                $oDiaOrigen->add($intervalo_dia);
            }
            echo 'destino: '.$oDiaDestinoS3->format('d-m-Y').'<---';
            echo 'origen: '.$oDiaOrigen->format('d-m-Y').'<br>';
            if ($i==7) {
                $oDiaOrigen->add($intervalo_dia);
            }
            echo 'destino: '.$oDiaDestinoS4->format('d-m-Y').'<---';
            echo 'origen: '.$oDiaOrigen->format('d-m-Y').'<br>';
            if ($i==7) {
                $oDiaOrigen->add($intervalo_dia);
            }
            echo 'destino: '.$oDiaDestinoS5->format('d-m-Y').'<---';
            echo 'origen: '.$oDiaOrigen->format('d-m-Y').'<br>';
        }
        if(($QTipoPlantillaOrigen== EncargoDia::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaOrigen== EncargoDia::PLANTILLA_MENSUAL_TRES)) {
            echo 'destinom: '.$oDiaDestinoS2->format('d-m-Y').'<---';
            echo 'origenm: '.$oDiaOrigenS2->format('d-m-Y').'<br>';
            echo 'destino: '.$oDiaDestinoS3->format('d-m-Y').'<---';
            echo 'origen: '.$oDiaOrigenS3->format('d-m-Y').'<br>';
            echo 'destino: '.$oDiaDestinoS4->format('d-m-Y').'<---';
            echo 'origen: '.$oDiaOrigenS4->format('d-m-Y').'<br>';
            echo 'destino: '.$oDiaDestinoS5->format('d-m-Y').'<---';
            echo 'origen: '.$oDiaOrigenS5->format('d-m-Y').'<br>';
            $oDiaOrigenS2->add($intervalo_dia);
            $oDiaOrigenS3->add($intervalo_dia);
            $oDiaOrigenS4->add($intervalo_dia);
            $oDiaOrigenS5->add($intervalo_dia);
        }
        $oDiaDestinoS2->add($intervalo_dia);
        $oDiaDestinoS3->add($intervalo_dia);
        $oDiaDestinoS4->add($intervalo_dia);
        $oDiaDestinoS5->add($intervalo_dia);
    }
    $oDiaOrigen->add($intervalo_dia);
    $oDiaDestino->add($intervalo_dia);
}


$oGesEncargoTipo = new GestorEncargoTipo();

$grupo = '8...';
$aWhere = [];
$aOperador = [];
$aWhere['id_tipo_enc'] = '^' . $grupo;
$aOperador['id_tipo_enc'] = '~';
$oGesEncargoTipo = new GestorEncargoTipo();
$cEncargoTipos = $oGesEncargoTipo->getEncargoTipos($aWhere, $aOperador);

$a_tipo_enc = [];
$posibles_encargo_tipo = [];
foreach ($cEncargoTipos as $oEncargoTipo) {
    if ($oEncargoTipo->getId_tipo_enc()>=8100) {
        $a_tipo_enc[] = $oEncargoTipo->getId_tipo_enc();
    }
}        

$EncargosZona = new EncargosZona($Qid_zona, $oInicio, $oFin, $orden);
$EncargosZona->setATipoEnc($a_tipo_enc);
$cEncargosZona = $EncargosZona->getEncargos();
foreach ($cEncargosZona as $oEncargo) {
    $id_enc = $oEncargo->getId_enc();
    $id_tipo = $oEncargo->getId_tipo_enc();
    $desc_enc = $oEncargo->getDesc_enc();

    $aWhere = [
        'id_enc' => $id_enc,
        'tstart' => "'$sInicio', '$sFin'",
    ];
    $aOperador = [
        'tstart' => 'BETWEEN',
    ];

    //Borro los encargos de la zona ya asignados en ese periodo
    $EncargoDiaRepository = new EncargoDiaRepository();
    $cEncargosaBorrar = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);
    foreach($cEncargosaBorrar as $oEncargoaBorrar) {
        $EncargoDiaRepository->Eliminar($oEncargoaBorrar);
    }
}

    
            $inicio_dia_plantilla = $dia_plantilla->format('Y-m-d').' 00:00:00';
            $fin_dia_plantilla = $dia_plantilla->format('Y-m-d').' 23:59:59';
            $aWhere = [
                'id_enc' => $id_enc,
                'tstart' => "'$inicio_dia_plantilla', '$fin_dia_plantilla'",
            ];
            $aOperador = [
                'tstart' => 'BETWEEN',
            ];
    
            if(($QTipoPlantilla== EncargoDia::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantilla== EncargoDia::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantilla== EncargoDia::PLANTILLA_MENSUAL_TRES)) {
                $inicio_dia_plantilla2 = $dia_plantilla2->format('Y-m-d').' 00:00:00';
                $fin_dia_plantilla2 = $dia_plantilla2->format('Y-m-d').' 23:59:59';
                $aWhere2 = [
                    'id_enc' => $id_enc,
                    'tstart' => "'$inicio_dia_plantilla2', '$fin_dia_plantilla2'",
                ];
                $aOperador2 = [
                    'tstart' => 'BETWEEN',
                ];
                $inicio_dia_plantilla3 = $dia_plantilla3->format('Y-m-d').' 00:00:00';
                $fin_dia_plantilla3 = $dia_plantilla3->format('Y-m-d').' 23:59:59';
                $aWhere3 = [
                    'id_enc' => $id_enc,
                    'tstart' => "'$inicio_dia_plantilla3', '$fin_dia_plantilla3'",
                ];
                $aOperador3 = [
                    'tstart' => 'BETWEEN',
                ];
            }
    
            $EncargoDiaRepository = new EncargoDiaRepository();
            $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);
     //       echo $aWhere['tstart'].$aOperador['tstart'].$aWhere['id_enc'].'<br>';
            if (count($cEncargosDia) > 1) {
                exit(_("sólo debería haber uno"));
            }
            if (count($cEncargosDia) === 1) {
                $oEncargoDia = $cEncargosDia[0];
                $id_nom = $oEncargoDia->getId_nom();
                $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                $hora_fin = $oEncargoDia->getTend()->format('H:i');
                $observ = $oEncargoDia->getObserv();
            }
                
    //si no hay nadie asignado para ese encargo vacio las variables
            if (count($cEncargosDia) === 0) {
                echo 'count encargosDia == 0: id_nom a NULL'.$id_nom.'<br>';
                $id_nom = null;
                $hora_ini = '';
                $hora_fin = '';
                $observ = '';
            }
    
    
            if((($QTipoPlantilla== EncargoDia::PLANTILLA_SEMANAL_TRES)||($QTipoPlantilla== EncargoDia::PLANTILLA_DOMINGOS_TRES)||($QTipoPlantilla== EncargoDia::PLANTILLA_MENSUAL_TRES))&&(!$ok_encargo))
            {
    //            echo 'SEGONA OPCIÓ<br>';
    //            echo $aWhere2['tstart'].$aOperador2['tstart'].$aWhere2['id_enc'].'<br>';
                $EncargoDiaRepository = new EncargoDiaRepository();
                $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere2,$aOperador2);
    
                if (count($cEncargosDia) > 1) {
                    exit(_("sólo debería haber uno"));
                }
                if (count($cEncargosDia) === 1) {
                    $oEncargoDia = $cEncargosDia[0];
                    $id_nom = $oEncargoDia->getId_nom();
    //                echo 'id_nom segona opcio:'.$id_nom.'<br>';
                    $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                    $hora_fin = $oEncargoDia->getTend()->format('H:i');
                    $observ = $oEncargoDia->getObserv();
                }
                        
            //si no hay nadie asignado para ese encargo vacio las variables
                if (count($cEncargosDia) === 0) {
                    $id_nom = null;
    //                echo 'id_nom'.$id_nom.'<br>';
                    $hora_ini = '';
                    $hora_fin = '';
                    $observ = '';
                }
            }
    
            if((($QTipoPlantilla== EncargoDia::PLANTILLA_SEMANAL_TRES)||($QTipoPlantilla== EncargoDia::PLANTILLA_DOMINGOS_TRES)||($QTipoPlantilla== EncargoDia::PLANTILLA_MENSUAL_TRES))&&(!$ok_encargo))
            {
    //            echo 'TERCERA OPCIÓN<br>';
                $EncargoDiaRepository = new EncargoDiaRepository();
                $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere3,$aOperador3);
            
                if (count($cEncargosDia) > 1) {
                    exit(_("sólo debería haber uno"));
                }
                if (count($cEncargosDia) === 1) {
                    $oEncargoDia = $cEncargosDia[0];
                    $id_nom = $oEncargoDia->getId_nom();
    //                echo 'id_nom tercera opcio:'.$id_nom.'<br>';
                    $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                    $hora_fin = $oEncargoDia->getTend()->format('H:i');
                    $observ = $oEncargoDia->getObserv();
                }
    
        //si no hay nadie asignado para ese encargo vacio las variables
                if (count($cEncargosDia) === 0) {
                    $id_nom = null;
    //                echo 'id_nom'.$id_nom.'<br>';
                    $hora_ini = '';
                    $hora_fin = '';
                    $observ = '';
                }
            }
        
            if ($ok_encargo)
            {
                echo 'OOOKKK_ENCARGO<br>';
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
                if ($EncargoDiaRepository->Guardar($oEncargoDia) === FALSE) {
                    $error_txt .= $EncargoDiaRepository->getErrorTxt();
                }  
            }
    
        
        

