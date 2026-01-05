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
    $oFinOrigen = new DateTimeLocal(EncargoDia::FIN_MENSUAL_UNO);
}

if($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_MENSUAL_TRES) {
    $oDiaOrigen2 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_DOS);
    $oDiaOrigen3 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_TRES);
}

if(($QTipoPlantillaDestino== EncargoDia::PLANTILLA_SEMANAL_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_SEMANAL_TRES)) {
    $oDiaDestino = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_UNO);
    $oFinDestino = new DateTimeLocal(EncargoDia::FIN_SEMANAL_UNO);
    $ndias=7;
}

if($QTipoPlantillaDestino== EncargoDia::PLANTILLA_SEMANAL_TRES) {
    $oDiaDestino2 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_DOS);
    $oDiaDestino3 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_TRES);
    $oFinDestino = new DateTimeLocal(EncargoDia::FIN_SEMANAL_TRES);
}

if(($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_TRES)) {
    $oDiaDestino = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_UNO);
    $oFinDestino = new DateTimeLocal(EncargoDia::FIN_DOMINGOS_UNO);
    $ndias=11;
}

if($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_TRES) {
    $oDiaDestino2 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_DOS);
//    $oFinDestino2 = new DateTimeLocal(EncargoDia::FIN_DOMINGOS_DOS);
    $oDiaDestino3 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_TRES);
    $oFinDestino = new DateTimeLocal(EncargoDia::FIN_DOMINGOS_TRES);
}

if(($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_TRES)) {
    $oDiaDestino = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oFinDestino = new DateTimeLocal(EncargoDia::FIN_MENSUAL_UNO);
    $ndias=35;
}

if($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_TRES) {
    $oDiaDestino2 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oDiaDestino3 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oFinDestino = new DateTimeLocal(EncargoDia::FIN_MENSUAL_TRES);
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

$sInicio_iso=$oDiaDestino->getIso();
$sFin_iso=$oFinDestino->getIso();
$orden='prioridad';
echo $sInicio_iso.'-'.$sFin_iso.'<br>';

$EncargosZona = new EncargosZona($Qid_zona, $oDiaDestino, $oFinDestino, $orden);
$EncargosZona->setATipoEnc($a_tipo_enc);
$cEncargosZona = $EncargosZona->getEncargos();
foreach ($cEncargosZona as $oEncargo) {
    $id_enc = $oEncargo->getId_enc();
    $desc_enc = $oEncargo->getDesc_enc();

    $aWhere = [
        'id_enc' => $id_enc,
        'tstart' => "'$sInicio_iso', '$sFin_iso'",
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





echo 'ndias: '.$ndias.'<br>';
for ($i=0;$i<$ndias;$i++) {
    echo 'I: '.$i.'<br>';
    $num_dia=$oDiaDestino->format('d-m-Y');
    $num_dia2=$oDiaDestino2->format('d-m-Y');
    $num_dia3=$oDiaDestino3->format('d-m-Y');
    echo 'destino: '.$oDiaDestino->format('d-m-Y').'<---';
    echo 'destino2: '.$oDiaDestino2->format('d-m-Y').'<---';
    echo 'destino3: '.$oDiaDestino3->format('d-m-Y').'<---';
    if(($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_SEMANAL_UNO) || ($QTipoPlantillaOrigen== EncargoDia::PLANTILLA_SEMANAL_TRES)) {
        $oDiaOrigen = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_UNO);
        $oDiaOrigen2 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_UNO);
        $oDiaOrigen3 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_UNO);
            $iOrigen=$i;
        //como se empieza el lunes, lunes+6 es domingo
        if ((($i>6) && ($i<11)) && (($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_TRES))) {
            $iOrigen=6;
        } 
        if (($i>6) && (($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_TRES))) {
            $iOrigen=$i%7;
        } 
        $iOrigen2=$iOrigen;
        $iOrigen3=$iOrigen;
    }
    if($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_SEMANAL_TRES) {
        $oDiaOrigen2 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_DOS);
        $oDiaOrigen3 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_TRES);
    }
    
    if(($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_DOMINGOS_TRES)) {
        $oDiaOrigen = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_UNO);
        $iOrigen=$i;
        //como se empieza el lunes, lunes+6 es domingo
        if (($i>6) && (($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_TRES))) {
            $iOrigen=$i%7;
            if ($iOrigen==6) {
                $iOrigen+=intdiv($i,7);
            }
        } 
    }
    if($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_DOMINGOS_TRES) {
        $oDiaOrigen2 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_DOS);
        $oDiaOrigen3 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_TRES);
    }
    
    if(($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_MENSUAL_TRES)) {
        $oDiaOrigen = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
        $iOrigen=$i;
        if ((($i>6) && ($i<11)) && (($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_TRES))) {
            $iOrigen=6+($i-6)*7;
        } 
    }
    
    if($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_MENSUAL_TRES) {
        $oDiaOrigen2 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_DOS);
        $oDiaOrigen3 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_TRES);
    }
    echo 'origen: '.$oDiaOrigen->format('d-m-Y').'--->';
    echo 'origen2: '.$oDiaOrigen2->format('d-m-Y').'--->';
    echo 'origen3: '.$oDiaOrigen3->format('d-m-Y').'--->';
    $oDiaOrigen->add(new DateInterval("P{$iOrigen}D"));
    echo 'origen: '.$oDiaOrigen->format('d-m-Y').'<br>';
    if (($QTipoPlantillaOrigen== EncargoDia::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantillaOrigen== EncargoDia::PLANTILLA_DOMINGOS_TRES)  || ($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_MENSUAL_TRES)){
        echo 'origen TRES';
        $oDiaOrigen2->add(new DateInterval("P{$iOrigen}D"));
        echo 'origen2: '.$oDiaOrigen2->format('d-m-Y').'<br>';
        $oDiaOrigen3->add(new DateInterval("P{$iOrigen}D"));
        echo 'origen3: '.$oDiaOrigen3->format('d-m-Y').'<br>';
    }

    $inicio_dia_plantilla = $oDiaOrigen->format('Y-m-d').' 00:00:00';
    $fin_dia_plantilla = $oDiaOrigen->format('Y-m-d').' 23:59:59';
    echo 'inicio dia platilla: '.$inicio_dia_plantilla.'<br>';

    foreach ($cEncargosZona as $oEncargo) {
        $id_enc = $oEncargo->getId_enc();
        $desc_enc = $oEncargo->getDesc_enc();

        echo 'foreach id_enc: '.$id_enc.'<br>';

        $aWhere = [
            'id_enc' => $id_enc,
            'tstart' => "'$inicio_dia_plantilla', '$fin_dia_plantilla'",
        ];
        $aOperador = [
            'tstart' => 'BETWEEN',
        ];
        $EncargoDiaRepository = new EncargoDiaRepository();
        $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);
        if (count($cEncargosDia) > 1) {
            echo 'III'.$inicio_dia_plantilla.'<br>';
            exit(_("sólo debería haber uno"));
        }
        if (count($cEncargosDia) === 1) {
            $oEncargoDia = $cEncargosDia[0];
            $id_nom = $oEncargoDia->getId_nom();
            $hora_ini = $oEncargoDia->getTstart()->format('H:i');
            $hora_fin = $oEncargoDia->getTend()->format('H:i');
            $observ = $oEncargoDia->getObserv();
            echo 'id_enc: '.$id_enc;
            echo ' desc_enc: '.$desc_enc;
            echo ' count:'.count($cEncargosDia);
            echo ' id_nom: '.$id_nom.'<br>';
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
        if (($QTipoPlantillaDestino== EncargoDia::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_TRES)  || ($QTipoPlantillaDestino == EncargoDia::PLANTILLA_MENSUAL_TRES)){
            $inicio_dia_plantilla = $oDiaOrigen2->format('Y-m-d').' 00:00:00';
            $fin_dia_plantilla = $oDiaOrigen2->format('Y-m-d').' 23:59:59';
            
            echo 'Origen2: inicio dia platilla: '.$inicio_dia_plantilla.'<br>';


            foreach ($cEncargosZona as $oEncargo) {
                $id_enc = $oEncargo->getId_enc();
                $desc_enc = $oEncargo->getDesc_enc();
            
                echo 'foreach id_enc: '.$id_enc.'<br>';

                $aWhere = [
                    'id_enc' => $id_enc,
                    'tstart' => "'$inicio_dia_plantilla', '$fin_dia_plantilla'",
                ];
                $aOperador = [
                    'tstart' => 'BETWEEN',
                ];
                $EncargoDiaRepository = new EncargoDiaRepository();
                $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);
                if (count($cEncargosDia) > 1) {
                    echo 'III1'.$inicio_dia_plantilla.'<br>';
                    exit(_("sólo debería haber uno"));
                }
                if (count($cEncargosDia) === 1) {
                    $oEncargoDia = $cEncargosDia[0];
                    $id_nom = $oEncargoDia->getId_nom();
                    $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                    $hora_fin = $oEncargoDia->getTend()->format('H:i');
                    $observ = $oEncargoDia->getObserv();
                    echo 'id_enc: '.$id_enc;
                    echo ' desc_enc: '.$desc_enc;
                    echo ' count:'.count($cEncargosDia);
                    echo ' id_nom: '.$id_nom.'<br>';
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
            }
        }

    }


    $oDiaDestino->add(new DateInterval('P1D'));
    if (($QTipoPlantillaDestino== EncargoDia::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_TRES)  || ($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_MENSUAL_TRES)){
        $oDiaDestino2->add(new DateInterval('P1D'));
        $oDiaDestino3->add(new DateInterval('P1D'));
    }
    echo 'I2: '.$i.'<br>';

}

/*
for ($i=1;$i<=7;$i++) {
    $num_dia=$oDiaDestino->format('d-m-Y');
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

*/