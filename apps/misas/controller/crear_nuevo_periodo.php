<?php


// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\EncargoConstants;
use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividad;
use actividadcargos\model\entity\GestorActividadCargo;
use encargossacd\model\entity\GestorEncargoTipo;
use misas\domain\repositories\EncargoDiaRepository;
use misas\domain\entity\EncargoDia;
use misas\domain\repositories\InicialesSacdRepository;
use misas\domain\entity\InicialesSacd;
use misas\domain\EncargoDiaId;
use misas\domain\EncargoDiaTend;
use misas\domain\EncargoDiaTstart;
use misas\model\EncargosZona;
use personas\model\entity\GestorPersona;
use personas\model\entity\PersonaSacd;
use personas\model\entity\PersonaEx;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use web\TiposActividades;
use zonassacd\model\entity\GestorZonaSacd;
use core\ValueObject;
use Ramsey\Uuid\Uuid as RamseyUuid;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$QTipoPlantilla = (string)filter_input(INPUT_POST, 'tipoplantilla');
$Qseleccion = (string)filter_input(INPUT_POST, 'seleccion');

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$menos_un_dia = new DateInterval('P1D');
$menos_un_dia->invert = 1;

echo 'zona:'.$Qid_zona.' tipoplantilla: '.$QTipoPlantilla.' periodo '.$Qperiodo.'<br>';

switch ($Qperiodo) {
    case "proxima_semana":
        $dia_week = date('N');
        echo 'dia:'.$dia_week.'<br>';
        $empiezamin = new DateTimeLocal(date('Y-m-d'));
        $intervalo='P'.(8-$dia_week).'D';
        $empiezamin->add(new DateInterval($intervalo));
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
        echo 'empieza'.$Qempiezamin_rep.'<br>';
        $intervalo='P7D';
        $empiezamax = $empiezamin;
        $empiezamax->add(new DateInterval($intervalo));
        $empiezamax->add($menos_un_dia);
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
        echo 'fin'.$Qempiezamax_rep.'<br>';
        break;
    case "proximo_mes":
        $proximo_mes = date('m') + 1;
        $anyo = date('Y');
        if ($proximo_mes == 12) {
            $proximo_mes = 1;
            $anyo++;
        }
        $empiezamin = new DateTimeLocal(date($anyo.'-'.$proximo_mes.'-01'));
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
        echo 'empieza'.$Qempiezamin_rep.'<br>';
        $siguiente_mes = $proximo_mes + 1;
        if ($siguiente_mes == 12) {
            $siguiente_mes = 1;
            $anyo++;
        }
        $empiezamax = new DateTimeLocal(date($anyo.'-'.$siguiente_mes.'-01'));
        $empiezamax->add($menos_un_dia);
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
        echo 'fin'.$Qempiezamax_rep.'<br>';
        break;
    default:
        $partes_min=explode('/',$Qempiezamin);
        $Qempiezamin_rep=$partes_min[2].'-'.$partes_min[1].'-'.$partes_min[0];
        $partes_max=explode('/',$Qempiezamax);
        $Qempiezamax_rep=$partes_max[2].'-'.$partes_max[1].'-'.$partes_max[0];
}

$sInicio=$Qempiezamin_rep.' 00:00:00';
$sFin=$Qempiezamax_rep.' 23:59:59';

$a_dias_semana_breve=[1=>'L', 2=>'M', 3=>'X', 4=>'J', 5=>'V', 6=>'S', 7=>'D'];
$a_nombre_mes_breve=[1=>'Ene', 2=>'feb', 3=>'mar', 4=>'abr', 5=>'may', 6=>'jun', 7=>'jul', 8=>'ago', 9=>'sep', 10=>'oct', 11=>'nov', 12=>'dic'];

$columns_cuadricula = [
    ["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 150, "cssClass" => "cell-title"],
];
$columns_sacd = [
    ["id" => "sacerdote", "name" => "Sacerdote", "field" => "sacerdote", "width" => 150, "cssClass" => "cell-title"],
];

//FALTA periode propera setmana i proper mes
//Funciona solament quan es dona data d'inici i final

$oInicio = new DateTimeLocal($sInicio);
$oFin = new DateTimeLocal($sFin);
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
$aOperador = array();
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

    $aWhereAct = [];
    $aOperadorAct = [];
    $aWhereAct['f_ini'] = "'$sFin'";
    $aOperadorAct['f_ini'] = '<=';
    $aWhereAct['f_fin'] = "'$sInicio'";
    $aOperadorAct['f_fin'] = '>=';
    $aWhereAct['status'] = ActividadAll::STATUS_ACTUAL;
    $aWhere = ['id_nom' => $id_nom];
    $aOperador = [];
            
    $oGesActividadCargo = new GestorActividadCargo();
    $cAsistentes = $oGesActividadCargo->getAsistenteCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);
            
    foreach ($cAsistentes as $aAsistente) {
        $id_activ = $aAsistente['id_activ'];
        $propio = $aAsistente['propio'];
        $id_cargo = empty($aAsistente['id_cargo']) ? '' : $aAsistente['id_cargo'];
            
// Seleccionar sólo las del periodo
        $aWhereAct['id_activ'] = $id_activ;
        $GesActividades = new GestorActividad();
        $cActividades = $GesActividades->getActividades($aWhereAct, $aOperadorAct);
        if (is_array($cActividades) && count($cActividades) == 0) continue;
            
        $oActividad = $cActividades[0]; // sólo debería haber una.
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $dInicioActividad = $oActividad->getF_ini();
        $sInicioActividad = $dInicioActividad->format('Y-m-d');
        $dFinActividad = $oActividad->getF_fin();
        $sFinActividad = $dFinActividad->format('Y-m-d');
        $h_ini = $oActividad->getH_ini();
        $h_fin = $oActividad->getH_fin();
        $dl_org = $oActividad->getDl_org();
        $nom_activ = $oActividad->getNom_activ();
        $oTipoActividad = new TiposActividades($id_tipo_activ);
        $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
        $nom_llarg = $nom_activ;

        if (isset($esta_sacd[$id_nom][$sInicioActividad])) {
            if ($esta_sacd[$id_nom][$sInicioActividad] == 1) {
                $esta_sacd[$id_nom][$sInicioActividad] = 2;  
            }
        }
        $esta_sacd[$id_nom][$sFinActividad] = -1;
        $dInicioActividadmas1 = date_add($dInicioActividad, $interval);
        $date_range_actividad = new DatePeriod($dInicioActividadmas1, $interval, $dFinActividad);
        foreach ($date_range_actividad as $date) {
            $num_dia = $date->format('Y-m-d');
            $esta_sacd[$id_nom][$num_dia] = 0;
            $donde_esta_sacd[$id_nom][$num_dia] = $nom_llarg;
        }

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

$data_cuadricula = [];
$orden='prioridad';

$EncargosZona = new EncargosZona($Qid_zona, $oInicio, $oFin, $orden);
$EncargosZona->setATipoEnc($a_tipo_enc);
$cEncargosZona = $EncargosZona->getEncargos();
foreach ($cEncargosZona as $oEncargo) {
    $id_enc = $oEncargo->getId_enc();
    $id_tipo = $oEncargo->getId_tipo_enc();
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

    $id_enc = $oEncargo->getId_enc();
    $desc_enc = $oEncargo->getDesc_enc();
//    echo $desc_enc.'<br>';
    $data_cols = [];
    $meta_dia = [];
    foreach ($date_range as $date) {
        $ok_encargo=false;
        $num_dia = $date->format('Y-m-d');
        $nom_dia = $date->format('D');
        if(($QTipoPlantilla== EncargoDia::PLANTILLA_SEMANAL_UNO) || ($QTipoPlantilla== EncargoDia::PLANTILLA_SEMANAL_TRES))
        {
            $dia_week = $date->format('N');
            $dia_plantilla = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_UNO);
            $intervalo_plantilla='P'.($dia_week-1).'D';
            $dia_plantilla->add(new DateInterval($intervalo_plantilla));
//            echo 'DIA PLANTILLA: '.$dia_plantilla->format('d-m-Y').'<br>';
        }

        if($QTipoPlantilla== EncargoDia::PLANTILLA_SEMANAL_TRES)
        {
            $dia_week = $date->format('N');
            $dia_plantilla2 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_DOS);
            $intervalo_plantilla='P'.($dia_week-1).'D';
            $dia_plantilla2->add(new DateInterval($intervalo_plantilla));
//            echo 'DIA PLANTILLA2: '.$dia_plantilla2->format('d-m-Y').'<br>';

            $dia_plantilla3 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_TRES);
            $intervalo_plantilla='P'.($dia_week-1).'D';
            $dia_plantilla3->add(new DateInterval($intervalo_plantilla));
//            echo 'DIA PLANTILLA3: '.$dia_plantilla3->format('d-m-Y').'<br>';
        }

        if(($QTipoPlantilla== EncargoDia::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantilla== EncargoDia::PLANTILLA_DOMINGOS_TRES))
        {
            $dia_week = $date->format('N');
            $dia_plantilla = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_UNO);

            if ($dia_week==7){
                $num_mes = $date->format('d');
                $num_semana = intdiv($num_mes,7);
//                echo 'DOMINGO:'.$num_mes.'=>'.$num_semana.'<br>';
                $intervalo_plantilla='P'.($dia_week+$num_semana-1).'D';
            } else {
                $intervalo_plantilla='P'.($dia_week-1).'D';
            }
            $dia_plantilla->add(new DateInterval($intervalo_plantilla));
//            echo 'DIA PLANTILLA: '.$dia_plantilla->format('d-m-Y').'<br>';
        }

        if($QTipoPlantilla== EncargoDia::PLANTILLA_DOMINGOS_TRES)
        {
//            echo 'tipo d2 OK<br>';
            $dia_week = $date->format('N');
            $dia_plantilla2 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_DOS);

            if ($dia_week==7){
                $num_mes = $date->format('d');
                $num_semana = intdiv($num_mes,7);
//                echo 'DOMINGO:'.$num_mes.'=>'.$num_semana.'<br>';
                $intervalo_plantilla='P'.($dia_week+$num_semana-1).'D';
            } else {
                $intervalo_plantilla='P'.($dia_week-1).'D';
            }
            $dia_plantilla2->add(new DateInterval($intervalo_plantilla));
//            echo 'DIA PLANTILLA2: '.$dia_plantilla2->format('d-m-Y').'<br>';

//echo 'tipo s3 OK<br>';
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

        if(($QTipoPlantilla== EncargoDia::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantilla== EncargoDia::PLANTILLA_MENSUAL_TRES))
        {
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

        if($QTipoPlantilla== EncargoDia::PLANTILLA_MENSUAL_TRES)
        {
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

        if(($QTipoPlantilla== EncargoDia::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantilla== EncargoDia::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantilla== EncargoDia::PLANTILLA_MENSUAL_TRES))
        {
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
 //           echo 'id_nom opcio 1:'.$id_nom.'<br>';
            $hora_ini = $oEncargoDia->getTstart()->format('H:i');
            $hora_fin = $oEncargoDia->getTend()->format('H:i');
            $observ = $oEncargoDia->getObserv();
        }
            
//si no hay nadie asignado para ese encargo vacio las variables
        if (count($cEncargosDia) === 0) {
//            echo 'id_nom a NULL'.$id_nom.'<br>';
            $id_nom = null;
            $hora_ini = '';
            $hora_fin = '';
            $observ = '';
        }


        if ($id_nom!=null) {
            $ok_encargo=true;
//            echo 'id_enc opcio 1:'.$id_enc.'tipo:'.$id_tipo.'esta: '.$esta_sacd[$id_nom][$num_dia].'<br>';
            //compruebo que no esté fuera
            if ($esta_sacd[$id_nom][$num_dia]>0) {
//                echo 'ESTA > 0<br>';
                if (($id_tipo>=8100) && ($id_tipo<8200)) {
                    //compruebo que no tenga otra misa por la mañana
                    if ($contador_1a_sacd[$id_nom][$num_dia]>0) {   
                        $ok_encargo=false;
//                        echo 'tendría dos misas por la mañana<br>';
                    }
                }
                if (($id_tipo>=8200) && ($id_tipo<8300)) {
                    //compruebo que no tenga tres misas en el día
//                    echo 'contador total: '.$contador_total_sacd[$id_nom][$num_dia].'<br>';
                    if ($contador_total_sacd[$id_nom][$num_dia]>1) {
                        $ok_encargo=false;
//                        echo 'tendría tres misas en el día<br>';
                    }
                }
            } else {
//                echo 'está fuera<br>';
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
//            echo 'TERCERA OPCIÓ<br>';
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
//            echo 'OOOKKK_ENCARGO<br>';
            $oEncargoDia = new EncargoDia();
            $Uuid = new EncargoDiaId(RamseyUuid::uuid4()->toString());
//                    $Uuid = new EncargoDiaId(uuid4()->toString);
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
 //               echo 'Missa a 1a<br>';
                $contador_1a_sacd[$id_nom][$num_dia]++;
                $contador_total_sacd[$id_nom][$num_dia]++;
            }
            if (($id_tipo>=8200) && ($id_tipo<8300)) {
//                echo 'Missa durant el dia<br>';
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

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('ver_cuadricula_zona.html.twig', $a_campos);
