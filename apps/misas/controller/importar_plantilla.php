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
    $oInicioOrigen = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_UNO);
    $oFinOrigen = new DateTimeLocal(EncargoDia::FIN_SEMANAL_UNO);
}

if($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_SEMANAL_TRES) {
    $oInicioOrigen2 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_DOS);
    $oFinOrigen2 = new DateTimeLocal(EncargoDia::FIN_SEMANAL_DOS);
    $oInicioOrigen3 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_TRES);
    $oFinOrigen3 = new DateTimeLocal(EncargoDia::FIN_SEMANAL_TRES);
}

if(($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_DOMINGOS_TRES)) {
    $oInicioOrigen = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_UNO);
    $oFinOrigen = new DateTimeLocal(EncargoDia::FIN_DOMINGOS_UNO);
}

if($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_DOMINGOS_TRES) {
    $oInicioOrigen2 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_DOS);
    $oFinOrigen2 = new DateTimeLocal(EncargoDia::FIN_DOMINGOS_DOS);
    $oInicioOrigen3 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_TRES);
    $oFinOrigen3 = new DateTimeLocal(EncargoDia::FIN_DOMINGOS_TRES);
}

if(($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_MENSUAL_TRES)) {
    $oInicioOrigen = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oFinOrigen = new DateTimeLocal(EncargoDia::FIN_MENSUAL_UNO);
}

if($QTipoPlantillaOrigen == EncargoDia::PLANTILLA_MENSUAL_TRES) {
    $oInicioOrigen = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_DOS);
    $oFinOrigen = new DateTimeLocal(EncargoDia::FIN_MENSUAL_DOS);
    $oInicioOrigen = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_TRES);
    $oFinOrigen = new DateTimeLocal(EncargoDia::FIN_MENSUAL_TRES);
}

if(($QTipoPlantillaDestino== EncargoDia::PLANTILLA_SEMANAL_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_SEMANAL_TRES)) {
    $oInicioDestino = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_UNO);
    $oFinDestino = new DateTimeLocal(EncargoDia::FIN_SEMANAL_UNO);
}

if($QTipoPlantillaDestino== EncargoDia::PLANTILLA_SEMANAL_TRES) {
    $oInicioDestino2 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_DOS);
    $oFinDestino2 = new DateTimeLocal(EncargoDia::FIN_SEMANAL_DOS);
    $oInicioDestino3 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_TRES);
    $oFinDestino3 = new DateTimeLocal(EncargoDia::FIN_SEMANAL_TRES);
}

if(($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_TRES)) {
    $oInicioDestino = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_UNO);
    $oFinDestino = new DateTimeLocal(EncargoDia::FIN_DOMINGOS_UNO);
}

if($QTipoPlantillaDestino== EncargoDia::PLANTILLA_DOMINGOS_TRES) {
    $oInicioDestino2 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_DOS);
    $oFinDestino2 = new DateTimeLocal(EncargoDia::FIN_DOMINGOS_DOS);
    $oInicioDestino3 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_TRES);
    $oFinDestino3 = new DateTimeLocal(EncargoDia::FIN_DOMINGOS_TRES);
}

if(($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_TRES)) {
    $oInicioDestino = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
    $oFinDestino = new DateTimeLocal(EncargoDia::FIN_MENSUAL_UNO);
}

if($QTipoPlantillaDestino== EncargoDia::PLANTILLA_MENSUAL_TRES) {
    $oInicioDestino2 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_DOS);
    $oFinDestino2 = new DateTimeLocal(EncargoDia::FIN_MENSUAL_DOS);
    $oInicioDestino3 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_TRES);
    $oFinDestino3 = new DateTimeLocal(EncargoDia::FIN_MENSUAL_TRES);
}
echo 'DIA INICIO PLANTILLA ORIGEN: '.$oInicioOrigen->format('d-m-Y').'<br>';
echo 'DIA FIN PLANTILLA ORIGEN: '.$oFinOrigen->format('d-m-Y').'<br>';
echo 'DIA INICIO PLANTILLA DESTINO: '.$oInicioDestino->format('d-m-Y').'<br>';
echo 'DIA FIN PLANTILLA DESTINO: '.$oFinDestino->format('d-m-Y').'<br>';
//echo 'DIA PLANTILLA2: '.$dia_plantilla2->format('d-m-Y').'<br>';
//echo 'DIA PLANTILLA3: '.$dia_plantilla3->format('d-m-Y').'<br>';


$interval = new DateInterval('P1D');
$date_range = new DatePeriod($oInicio, $interval, $oFin);
$a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
foreach ($date_range as $date) {
    $num_dia = $date->format('Y-m-d');
    $dia_week = $date->format('N');
    $dia_mes = $date->format('d');
    $nom_dia=$a_dias_semana_breve[$dia_week].' '.$dia_mes;
    $columns_cuadricula[] =
        ["id" => "$num_dia", "name" => "$nom_dia", "field" => "$num_dia", "width" => 80, "cssClass" => "cell-title"];
    $columns_cuadricula[] =
        ["id" => "$num_dia", "name" => "$nom_dia", "field" => "$num_dia", "width" => 80, "cssClass" => "cell-title"];
}

//        $data_sacd = [];
$aWhere = [];
$aWhere['id_zona'] = $Qid_zona;
$aOperador = [];
$GesZonasSacd = new GestorZonaSacd();
$cZonaSacd = $GesZonasSacd->getZonasSacds($aWhere, $aOperador);
$contador_1a_sacd = [];
$contador_total_sacd = [];
$esta_sacd = [];
$donde_esta_sacd = [];
foreach ($cZonaSacd as $oZonaSacd) {
    $id_nom = $oZonaSacd->getId_nom();
    $contador_sacd[$id_nom] = [];
    $InicialesSacd = new InicialesSacd();
    $nombre_sacd=$InicialesSacd->nombre_sacd($id_nom);
    $contador_sacd[$id_nom]['nombre']=$nombre_sacd;
    foreach ($date_range as $date) {
        $num_dia = $date->format('Y-m-d');
        $contador_1a_sacd[$id_nom][$num_dia] = 0;    
        $contador_total_sacd[$id_nom][$num_dia] = 0;    
        $esta_sacd[$id_nom][$num_dia] = 1;    
    }
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

$orden='prioridad';

$hay_bendicion=[];
// Miro qué días hay bendición
foreach ($date_range as $date) {
    $bendicion='NO';

    $dia_completo=$date->format('Y-m-d');
    $dia_semana=date('w', strtotime($dia_completo));
        
    $temps='';
        
    $partes=explode('-', $dia_completo);
    $dia=intval($partes[2]);
    $mes=intval($partes[1]);
    $anyo=intval($partes[0]);
//    $date_dia_completo= new DateTime($dia_completo);
    echo 'd-m-Y'.$dia.'.'.$mes.'.'.$anyo.'<br>';
    //DPascua
        
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

    echo $desc_enc.'-'.$id_tipo.'<br>';
//Si el encargo es una bendición miro si hay bendición ese día. 
    
    
    $data_cols = [];
    $meta_dia = [];
    foreach ($date_range as $date) {
        $ok_encargo=false;
        $dia_completo=$date->format('Y-m-d');

        if (($id_tipo!=8300) || ($hay_bendicion[$dia_completo]=='SI')) {
            $num_dia = $date->format('Y-m-d');
            $nom_dia = $date->format('D');
            if(($QTipoPlantilla== EncargoDia::PLANTILLA_SEMANAL_UNO) || ($QTipoPlantilla== EncargoDia::PLANTILLA_SEMANAL_TRES)) {
                $dia_week = $date->format('N');
                $dia_plantilla = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_UNO);
                $intervalo_plantilla='P'.($dia_week-1).'D';
                $dia_plantilla->add(new DateInterval($intervalo_plantilla));
                echo 'DIA PLANTILLA: '.$dia_plantilla->format('d-m-Y').'<br>';
            }
    
            if($QTipoPlantilla== EncargoDia::PLANTILLA_SEMANAL_TRES) {
                $dia_week = $date->format('N');
                $dia_plantilla2 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_DOS);
                $intervalo_plantilla='P'.($dia_week-1).'D';
                $dia_plantilla2->add(new DateInterval($intervalo_plantilla));
    
                $dia_plantilla3 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_TRES);
                $intervalo_plantilla='P'.($dia_week-1).'D';
                $dia_plantilla3->add(new DateInterval($intervalo_plantilla));
            }
    
            if(($QTipoPlantilla== EncargoDia::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantilla== EncargoDia::PLANTILLA_DOMINGOS_TRES)) {
                $dia_week = $date->format('N');
                $dia_plantilla = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_UNO);
    
                if ($dia_week==7){
                    $num_mes = $date->format('d');
                    $num_semana = intdiv($num_mes,7);
                    $intervalo_plantilla='P'.($dia_week+$num_semana-1).'D';
                } else {
                    $intervalo_plantilla='P'.($dia_week-1).'D';
                }
                $dia_plantilla->add(new DateInterval($intervalo_plantilla));
            }
    
            if($QTipoPlantilla== EncargoDia::PLANTILLA_DOMINGOS_TRES) {
                $dia_week = $date->format('N');
                $dia_plantilla2 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_DOS);
    
                if ($dia_week==7){
                    $num_mes = $date->format('d');
                    $num_semana = intdiv($num_mes,7);
                    $intervalo_plantilla='P'.($dia_week+$num_semana-1).'D';
                } else {
                    $intervalo_plantilla='P'.($dia_week-1).'D';
                }
                $dia_plantilla2->add(new DateInterval($intervalo_plantilla));
    
                $dia_plantilla3 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_TRES);
    
                if ($dia_week==7){
                    $num_mes = $date->format('d');
                    $num_semana = intdiv($num_mes,7);
    //                echo 'DOMINGO:'.$num_mes.'=>'.$num_semana.'<br>';
                    $intervalo_plantilla='P'.($dia_week+$num_semana-1).'D';
                } else {
                    $intervalo_plantilla='P'.($dia_week-1).'D';
                }
                $dia_plantilla3->add(new DateInterval($intervalo_plantilla));
    //            echo 'DIA PLANTILLA3: '.$dia_plantilla3->format('d-m-Y').'<br>';
    
            }
    
            if(($QTipoPlantilla== EncargoDia::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantilla== EncargoDia::PLANTILLA_MENSUAL_TRES)) {
    //            echo 'tipo mensual 1 ó 3 OK<br>';
                $dia_week = $date->format('N');
                $dia_plantilla = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
                $num_mes = $date->format('d');
                $num_semana = intdiv($num_mes,7);
    //            echo 'MENSUAL:'.$num_mes.'=>'.$num_semana.'<br>';
                $intervalo_plantilla='P'.($dia_week+$num_semana-1).'D';
                $dia_plantilla->add(new DateInterval($intervalo_plantilla));
    //            echo 'DIA PLANTILLA: '.$dia_plantilla->format('d-m-Y').'<br>';
            }
    
            if($QTipoPlantilla== EncargoDia::PLANTILLA_MENSUAL_TRES) {
    //            echo 'tipo m2 OK<br>';
                $dia_week = $date->format('N');
                $dia_plantilla2 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_DOS);
                $num_mes = $date->format('d');
                $num_semana = intdiv($num_mes,7);
    //            echo 'MENSUAL:'.$num_mes.'=>'.$num_semana.'<br>';
                $intervalo_plantilla='P'.($dia_week+$num_semana-1).'D';
                $dia_plantilla2->add(new DateInterval($intervalo_plantilla));
    //            echo 'DIA PLANTILLA2: '.$dia_plantilla2->format('d-m-Y').'<br>';
    
    //            echo 'tipo s3 OK<br>';
                $dia_plantilla3 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_TRES);
                $num_mes = $date->format('d');
                $num_semana = intdiv($num_mes,7);
    //            echo 'MENSUAL:'.$num_mes.'=>'.$num_semana.'<br>';
                $intervalo_plantilla='P'.($dia_week+$num_semana-1).'D';
                $dia_plantilla3->add(new DateInterval($intervalo_plantilla));
    //            echo 'DIA PLANTILLA3: '.$dia_plantilla3->format('d-m-Y').'<br>';
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
                echo 'id_nom opcio 1:'.$id_nom.'<br>';
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
    
    
            if ($id_nom!=null) {
                $ok_encargo=true;
                //compruebo que no esté fuera
                if(!isset($esta_sacd[$id_nom][$num_dia])) {
                    $esta_sacd[$id_nom][$num_dia]=1;
                }
                echo 'id_enc opcio 1:'.$id_enc.'tipo:'.$id_tipo.'esta: '.$esta_sacd[$id_nom][$num_dia].'<br>';
                if ($esta_sacd[$id_nom][$num_dia]>0) {
                    echo 'ESTA > 0<br>';
                    if (($id_tipo>=8100) && ($id_tipo<8200)) {
                        //compruebo que no tenga otra misa por la mañana
                        if ($contador_1a_sacd[$id_nom][$num_dia]>0) {   
                            $ok_encargo=false;
                            echo 'tendría dos misas por la mañana<br>';
                        }
                    }
                    if (($id_tipo>=8200) && ($id_tipo<8300)) {
                        //compruebo que no tenga tres misas en el día
                        echo 'contador total: '.$contador_total_sacd[$id_nom][$num_dia].'<br>';
                        if ($contador_total_sacd[$id_nom][$num_dia]>1) {
                            $ok_encargo=false;
                            echo 'tendría tres misas en el día<br>';
                        }
                    }
                } else {
                    echo 'está fuera<br>';
                    $ok_encargo=false;
                }
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
    
                if ($id_nom!=null) {
                    $ok_encargo=true;
    //                echo 'id_enc:'.$id_enc.'tipo:'.$id_tipo.'<br>';
                    //compruebo que no esté fuera
                    if ($esta_sacd[$id_nom][$num_dia]>0) {
                        if (($id_tipo>=8100) && ($id_tipo<8200)) {
                            //compruebo que no tenga otra misa por la mañana
    //                        echo 'contador 1a: '.$contador_1a_sacd[$id_nom][$num_dia].'<br>';
                            if ($contador_1a_sacd[$id_nom][$num_dia]>0) {   
                                $ok_encargo=false;
    //                            echo 'tendría dos misas por la mañana<br>';
                            }
                        }
                        if (($id_tipo>=8200) && ($id_tipo<8300)) {
                            //compruebo que no tenga tres misas en el día
    //                        echo 'contador total: '.$contador_total_sacd[$id_nom][$num_dia].'<br>';
                            if ($contador_total_sacd[$id_nom][$num_dia]>1) {
                                $ok_encargo=false;
                                echo 'tendría tres misas en el día<br>';
                            }
                        }
                    } else {
                        echo 'está fuera<br>';
                        $ok_encargo=false;
                    }
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
        
        
                if ($id_nom!=null) {
                    $ok_encargo=true;
    //                echo 'id_enc:'.$id_enc.'tipo:'.$id_tipo.'<br>';
                    //compruebo que no esté fuera
                    if ($esta_sacd[$id_nom][$num_dia]>0) {
                        if (($id_tipo>=8100) && ($id_tipo<8200)) {
                            //compruebo que no tenga otra misa por la mañana
     //                       echo 'contador 1a: '.$contador_1a_sacd[$id_nom][$num_dia].'<br>';
                            if ($contador_1a_sacd[$id_nom][$num_dia]>0) {   
                                $ok_encargo=false;
     //                           echo 'tendría dos misas por la mañana<br>';
                            }
                        }
                        if (($id_tipo>=8200) && ($id_tipo<8300)) {
                            //compruebo que no tenga tres misas en el día
     //                       echo 'contador total: '.$contador_total_sacd[$id_nom][$num_dia].'<br>';
                            if ($contador_total_sacd[$id_nom][$num_dia]>1) {
                                $ok_encargo=false;
     //                           echo 'tendría tres misas en el día<br>';
                            }
                        }
                    } else {
     //                   echo 'está fuera<br>';
                        $ok_encargo=false;
                    }
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
                if (($id_tipo>=8100) && ($id_tipo<8200)) {
                    echo 'Missa a 1a<br>';
                    $contador_1a_sacd[$id_nom][$num_dia]++;
                    $contador_total_sacd[$id_nom][$num_dia]++;
                }
                if (($id_tipo>=8200) && ($id_tipo<8300)) {
                    echo 'Missa durant el dia<br>';
                    $contador_total_sacd[$id_nom][$num_dia]++;
                }
            }
            else {
                //si no hay nadie asignado para ese encargo vacío las variables
                if (count($cEncargosDia) === 0) {
                    $id_nom = null;
    //                echo 'id_nom'.$id_nom.'<br>';
                    $hora_ini = '';
                    $hora_fin = '';
                    $observ = '';
                }
            }
    
            $data_cols["$num_dia"] = " -- ";
    
            $meta_dia["$num_dia"] = [
                "uuid_item" => "",
                "color" => "",
                "key" => '',
                "tstart" => '',
                "tend" => '',
                "observ" => '',
                "id_enc" => $id_enc,
            ];
    
            // sobreescribir los que tengo datos:
            $inicio_dia = $num_dia.' 00:00:00';
            $fin_dia = $num_dia.' 23:59:59';
            $aWhere = [
                'id_enc' => $id_enc,
                'tstart' => "'$inicio_dia', '$fin_dia'",
            ];
            $aOperador = [
                'tstart' => 'BETWEEN',
            ];
            $EncargoDiaRepository = new EncargoDiaRepository();
            $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);
    
            if (count($cEncargosDia) > 1) {
                exit(_("sólo debería haber uno"));
            }
    
            if (count($cEncargosDia) === 1) {
                $oEncargoDia = $cEncargosDia[0];
                $id_nom = $oEncargoDia->getId_nom();
                $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                if ($hora_ini=='00:00')
                    $hora_ini='';
                $InicialesSacd = new InicialesSacd();
                $iniciales=$InicialesSacd->iniciales($id_nom);
                $color = '';
                if (!isset($esta_sacd[$id_nom]))
                    $color='verdeclaro';
                $meta_dia["$num_dia"] = [
                    "uuid_item" => $oEncargoDia->getUuid_item()->value(),
                    "color" => $color,
                    "key" => "$id_nom#$iniciales",
                    "tstart" => $oEncargoDia->getTstart()->getHora(),
                    "tend" => $oEncargoDia->getTend()->getHora(),
                    "observ" => $oEncargoDia->getObserv(),
                    "id_enc" => $id_enc,
                ];
    //                echo 'contador 1a: '.$contador_1a_sacd[$id_nom][$num_dia].'<br>';
    
                // añadir '*' si tiene observaciones
                $iniciales .= empty($oEncargoDia->getObserv())? '' : '*';
                $data_cols["$num_dia"] = $iniciales." ".$hora_ini;
            }
        }
        
    }   


    $data_cols["encargo"] = $desc_enc;  
    $data_cols["meta"] = $meta_dia;
    // añado una columna 'meta' con metadatos, invisible, porque no está
    // en la definición de columns
    $data_cuadricula[] = $data_cols;
}


$json_columns_cuadricula = json_encode($columns_cuadricula);
$json_data_cuadricula = json_encode($data_cuadricula);

$oHash = new Hash();
$oHash->setCamposForm('color!dia!id_enc!key!observ!tend!tstart!uuid_item');
$array_h = $oHash->getParamAjaxEnArray();


$a_campos = ['oPosicion' => $oPosicion,
    'json_columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
    'array_h' => $array_h,
];

$oView = new ViewTwig('misas/controller');
echo $oView->render('ver_cuadricula_zona.html.twig', $a_campos);
