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

//Se ha de eliminar esta función
function iniciales($id_nom) {
    if ($id_nom>0) {
        $PersonaSacd = new PersonaSacd($id_nom);
        $nom = $PersonaSacd->getNom();
        $ap1 = $PersonaSacd->getApellido1();
        $ap2 = $PersonaSacd->getApellido2();
    } else {
        $PersonaEx = new PersonaEx($id_nom);
        $sacdEx = $PersonaEx->getNombreApellidos();
        $nom = $PersonaEx->getNom();
        $ap1 = $PersonaEx->getApellido1();
        $ap2 = $PersonaEx->getApellido2();
    }

    // iniciales
    $inom='';
    if (!is_null($nom))
        $inom = mb_substr($nom, 0, 1);
    $iap1='';
    if (!is_null($ap1))
        $iap1 = mb_substr($ap1, 0, 1);
    $iap2='';
    if (!is_null($ap2))
        $iap2 = mb_substr($ap2, 0, 1);

    $iniciales = strtoupper($inom . $iap1 . $iap2);
    return $iniciales;
}

function esta_fuera($id_nom, $inicio, $fin) {
    return 'FUERA OK '.$id_nom.'<br>';
}



$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$QTipoPlantilla = (string)filter_input(INPUT_POST, 'TipoPlantilla');
$Qseleccion = (string)filter_input(INPUT_POST, 'seleccion');

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$partes_min=explode('/',$Qempiezamin);
$Qempiezamin_rep=$partes_min[2].'-'.$partes_min[1].'-'.$partes_min[0];
$partes_max=explode('/',$Qempiezamax);
$Qempiezamax_rep=$partes_max[2].'-'.$partes_max[1].'-'.$partes_max[0];
$sInicio=$Qempiezamin_rep.' 00:00:00';
$sFin=$Qempiezamax_rep.' 23:59:59';

echo 'Tipo: '.$QTipoPlantilla.'<br>';
echo 'HOLA: '.$Qempiezamin_rep.' - '.$Qempiezamax_rep.'<br>';

$a_dias_semana_breve=[1=>'L', 2=>'M', 3=>'X', 4=>'J', 5=>'V', 6=>'S', 7=>'D'];

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
            //$nom_dia = $date->format('D');
            $dia_week = $date->format('N');
            $dia_mes = $date->format('d');
            //$nom_dia = $a_dias_semana[$dia_week];
            $nom_dia=$a_dias_semana_breve[$dia_week].' '.$dia_mes;
echo 'nom_dia: '.$nom_dia.'<br>';
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
        $contador_sacd = [];
        $esta_sacd = [];
        foreach ($cZonaSacd as $oZonaSacd) {
//            $data_cols_sacd = [];
            $id_nom = $oZonaSacd->getId_nom();
            $contador_sacd[$id_nom] = [];
//            $InicialesSacdRepository = new InicialesSacdRepository();
//            $nombre_sacd=$InicialesSacdRepository->nombre_sacd($id_nom);
            $InicialesSacd = new InicialesSacd();
            $nombre_sacd=$InicialesSacd->nombre_sacd($id_nom);
echo 'NOMBRE:'.$nombre_sacd.' '.$id_nom.'<br>';
            $contador_sacd[$id_nom]['nombre']=$nombre_sacd;
            foreach ($date_range as $date) {
                $num_dia = $date->format('Y-m-d');
//                $data_cols_sacd[$num_dia] = 0;    
                $contador_sacd[$id_nom][$num_dia] = 0;    
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
            echo 'actividad: '.$id_activ;
                    $propio = $aAsistente['propio'];
            //        $plaza = $aAsistente['plaza'];
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
            echo ' nom: '.$nom_activ.'<br>';
                    $oTipoActividad = new TiposActividades($id_tipo_activ);
                        $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                        $nom_llarg = $nom_activ;
            echo 'nom_curt:'.$nom_curt.'nom llarg: '.$nom_llarg.'<br>';
            echo $sInicioActividad.'='.$h_ini.'-->'.$sFinActividad.'='.$h_fin.'<br>';
                $esta_sacd[$id_nom][$sInicioActividad] = 2;  
                $esta_sacd[$id_nom][$sFinActividad] = -1;          
                $date_range_actividad = new DatePeriod($dInicioActividad, $interval, $dFinActividad);
                foreach ($date_range_actividad as $date) {
                    $num_dia = $date->format('Y-m-d');
//                $data_cols_sacd[$num_dia] = 0;    
                    $esta_sacd[$id_nom][$num_dia] = 0;
                }

            }
//            $data_sacd[]=$data_cols_sacd;
            
            echo 'AUSENCIAS:<br><br>';
            foreach ($date_range as $date) {
                $num_dia = $date->format('Y-m-d');
                echo $esta_sacd[$id_nom][$num_dia].' ';
            }
            echo '<br><br>';
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
    $aWhere = [
        'id_enc' => $id_enc,
        'tstart' => "'$sInicio', '$sFin'",
    ];
    $aOperador = [
        'tstart' => 'BETWEEN',
    ];

    //Borro los encargos ya asignados en ese periodo
    $EncargoDiaRepository = new EncargoDiaRepository();
    $cEncargosaBorrar = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);
    foreach($cEncargosaBorrar as $oEncargoaBorrar) {
//        $id_enc=$oEncargoaBorrar->getId_enc();
//        $Uuid = $oEncargoaBorrar->getUuid_item();
//        echo 'id_enc'.$id_enc.'-'.$Uuid.'<br>';
        $EncargoDiaRepository->Eliminar($oEncargoaBorrar);
    }

    $id_enc = $oEncargo->getId_enc();
    $desc_enc = $oEncargo->getDesc_enc();
echo $desc_enc.'<br>';
    $data_cols = [];
    $meta_dia = [];
    foreach ($date_range as $date) {
        $num_dia = $date->format('Y-m-d');
        $nom_dia = $date->format('D');
echo 'dia: '.$num_dia.$nom_dia.'--'.$QTipoPlantilla.'<br>';
        if($QTipoPlantilla=='s')
        {
            echo 'tipo s OK<br>';
            $dia_week = $date->format('N');
            $dia_plantilla= '2001-01-'.$dia_week;
            echo 'dia '.$dia_plantilla.'<br>';
            $inicio_dia_plantilla = $dia_plantilla.' 00:00:00';
            $fin_dia_plantilla = $dia_plantilla.' 23:59:59';
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
                exit(_("sólo debería haber uno"));
            }
            if (count($cEncargosDia) === 1) {
                $oEncargoDia = $cEncargosDia[0];
                $id_nom = $oEncargoDia->getId_nom();
                echo 'id_nom'.$id_nom.'<br>';
                $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                $hora_fin = $oEncargoDia->getTend()->format('H:i');
                $observ = $oEncargoDia->getObserv();
            }
            
//si no hay nadie asignado para ese encargo vacio las variables
            if (count($cEncargosDia) === 0) {
                $id_nom = null;
                echo 'id_nom'.$id_nom.'<br>';
                $hora_ini = '';
                $hora_fin = '';
                $observ = '';
            }


        if ($id_nom!=null) {
    
            //compruebo que no esté fuera
            $inicio_nuevo_dia = $num_dia.' 00:00:00';
            $fin_nuevo_dia = $num_dia.' 23:59:59';
            $aWhere = [
                'id_enc' => $id_enc,
                'tstart' => "'$inicio_nuevo_dia', '$fin_nuevo_dia'",
            ];
            $aOperador = [
                'tstart' => 'BETWEEN',
            ];
            $EncargoNuevoDiaRepository = new EncargoDiaRepository();
            $cEncargoNuevoDia = $EncargoNuevoDiaRepository->getEncargoDias($aWhere,$aOperador);
    
            if (empty($cEncargoNuevoDia)) {
echo 'empty<br>';
                if ($id_nom!=null) {
                    echo 'gravo nou:'.$id_nom.'<br>';
                    $oEncargoDia = new EncargoDia();
                    $Uuid = new EncargoDiaId(RamseyUuid::uuid4()->toString());
//                $Uuid = new EncargoDiaId(uuid4()->toString);
                    $oEncargoDia->setUuid_item($Uuid);
                    $oEncargoDia->setId_nom($id_nom);
                    $tstart = new EncargoDiaTstart($num_dia, $hora_ini);
                    $oEncargoDia->setTstart($tstart);
            
                    $tend = new EncargoDiaTend($num_dia, $hora_fin);
                    $oEncargoDia->setTend($tend);

                    if (isset($observ))
                        $oEncargoDia->setObserv($observ);
                    $oEncargoDia->setId_enc($id_enc);
                    if ($EncargoDiaRepository->Guardar($oEncargoDia) === FALSE) {
                        $error_txt .= $EncargoDiaRepository->getErrorTxt();
                    }
                }
            } else {
                echo 'ple i els hauria de haver borrat abans<br>';
                echo $id_nom.'<br>';
                // debería haber solamente uno
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
            $iniciales = iniciales($id_nom);
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
