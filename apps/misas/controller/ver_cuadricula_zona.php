<?php


// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\EncargoConstants;
use misas\domain\repositories\EncargoDiaRepository;
use misas\domain\repositories\InicialesSacdRepository;
use misas\domain\entity\InicialesSacd;
use misas\domain\entity\EncargoDia;
use misas\model\EncargosZona;
use personas\model\entity\PersonaSacd;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoTipo;
use zonassacd\model\entity\GestorZonaSacd;
use web\DateTimeLocal;
//use web\Desplegable;
use web\Hash;
//use zonassacd\model\entity\GestorZonaSacd;
//use personas\model\entity\GestorPersona;
use personas\model\entity\PersonaEx;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$QTipoPlantilla = (string)filter_input(INPUT_POST, 'tipo_plantilla');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qorden = (string)filter_input(INPUT_POST, 'orden');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

switch ($Qperiodo) {
    case "semana_next":
        $dia_week = date('N');
        $empiezamin = new DateTimeLocal(date('Y-m-d'));
        $intervalo='P'.(8-$dia_week).'D';
        $empiezamin->add(new DateInterval($intervalo));
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
        $intervalo='P7D';
        $empiezamax = $empiezamin;
        $empiezamax->add(new DateInterval($intervalo));
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
        break;
    case "mes_next":
        $proximo_mes = date('m') + 1;
        $anyo = date('Y');
        if ($proximo_mes == 12) {
            $proximo_mes = 1;
            $anyo++;
        }
        $empiezamin = new DateTimeLocal(date($anyo.'-'.$proximo_mes.'-01'));
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
        $siguiente_mes = $proximo_mes + 1;
        if ($siguiente_mes == 12) {
            $siguiente_mes = 1;
            $anyo++;
        }
        $empiezamax = new DateTimeLocal(date($anyo.'-'.$siguiente_mes.'-01'));
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
        break;
    default:
    $Qempiezamin_rep=str_replace('/','-',$Qempiezamin);
    $Qempiezamax_rep=str_replace('/','-',$Qempiezamax);
}

if(isset($Qorden) && ($Qorden!==null))
    $Qorden='desc_enc';

$a_dias_semana_breve=[1=>'L', 2=>'M', 3=>'X', 4=>'J', 5=>'V', 6=>'S', 7=>'D'];
$a_nombre_mes_breve=[1=>'Ene', 2=>'feb', 3=>'mar', 4=>'abr', 5=>'may', 6=>'jun', 7=>'jul', 8=>'ago', 9=>'sep', 10=>'oct', 11=>'nov', 12=>'dic'];


$columns_cuadricula = [
    ["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 250, "cssClass" => "cell-title"],
];
$columns_sacd = [
    ["id" => "sacerdote", "name" => "Sacerdote", "field" => "sacerdote", "width" => 250, "cssClass" => "cell-title"],
];

$dia_week_sacd = [];
switch (trim($QTipoPlantilla)) {
    case EncargoDia::PLANTILLA_SEMANAL_UNO:
    case EncargoDia::PLANTILLA_SEMANAL_TRES:
        $oInicio = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_UNO);
        $oFin = new DateTimeLocal(EncargoDia::FIN_SEMANAL_UNO);
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($oInicio, $interval, $oFin);
        if ($QTipoPlantilla == EncargoDia::PLANTILLA_SEMANAL_TRES) {
            $oInicio2 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_DOS);
            $oFin2 = new DateTimeLocal(EncargoDia::FIN_SEMANAL_DOS);
            $date_range2 = new DatePeriod($oInicio2, $interval, $oFin2);
            $oInicio3 = new DateTimeLocal(EncargoDia::INICIO_SEMANAL_TRES);
            $oFin3 = new DateTimeLocal(EncargoDia::FIN_SEMANAL_TRES);
            $date_range3 = new DatePeriod($oInicio3, $interval, $oFin3);
            $interval3=New DateInterval(EncargoDia::INTERVAL_SEMANAL);
        }
        $a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
        foreach ($date_range as $date) {
            $num_dia = $date->format('Y-m-d');
            $dia_week = $date->format('N');
            $dia_week_sacd[$num_dia] = $date->format('N');
            $nom_dia = $a_dias_semana[$dia_week];
        
            $columns_cuadricula[] = [
                "id" => "$num_dia", 
                "name" => "$nom_dia", 
                "field" => "$num_dia", 
                "width" => 80, 
                "cssClass" => "cell-title"
            ];
            $columns_sacd[] = [
                "id" => "$num_dia", 
                "name" => "$nom_dia", 
                "field" => "$num_dia", 
                "width" => 80, 
                "cssClass" => "cell-title"];
        }
        break;
    case EncargoDia::PLANTILLA_DOMINGOS_UNO:
    case EncargoDia::PLANTILLA_DOMINGOS_TRES:
        $oInicio = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_UNO);
        $oFin = new DateTimeLocal(EncargoDia::FIN_DOMINGOS_UNO);
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($oInicio, $interval, $oFin);
        if ($QTipoPlantilla == EncargoDia::PLANTILLA_DOMINGOS_TRES) {
            $oInicio2 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_DOS);
            $oFin2 = new DateTimeLocal(EncargoDia::FIN_DOMINGOS_DOS);
            $date_range2 = new DatePeriod($oInicio2, $interval, $oFin2);
            $oInicio3 = new DateTimeLocal(EncargoDia::INICIO_DOMINGOS_TRES);
            $oFin3 = new DateTimeLocal(EncargoDia::FIN_DOMINGOS_TRES);
            $date_range3 = new DatePeriod($oInicio3, $interval, $oFin3);
            $interval3=New DateInterval(EncargoDia::INTERVAL_DOMINGOS);
        }
        $a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
        foreach ($date_range as $date) {
            $num_dia = $date->format('Y-m-d');
            $dia_week = $date->format('N');
            $dia_week_sacd[$num_dia] = $date->format('N');
            $dia_mes = $date->format('d');
            if ($dia_mes<7) {
                $nom_dia = $a_dias_semana[$dia_week];
            } else {
                $nom_dia = 'domingo '.strval($dia_mes-6);
                $dia_week_sacd[$num_dia] = 7;
            }
        
            $columns_cuadricula[] = [
                "id" => "$num_dia", 
                "name" => "$nom_dia", 
                "field" => "$num_dia", 
                "width" => 80, 
                "cssClass" => "cell-title"
            ];
            $columns_sacd[] = [
                "id" => "$num_dia", 
                "name" => "$nom_dia", 
                "field" => "$num_dia", 
                "width" => 80, 
                "cssClass" => "cell-title"];
        }
        break;
    case EncargoDia::PLANTILLA_MENSUAL_UNO:
    case EncargoDia::PLANTILLA_MENSUAL_TRES:
        $oInicio = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_UNO);
        $oFin = new DateTimeLocal(EncargoDia::FIN_MENSUAL_UNO);
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($oInicio, $interval, $oFin);
        if ($QTipoPlantilla == EncargoDia::PLANTILLA_MENSUAL_TRES) {
            $oInicio2 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_DOS);
            $oFin2 = new DateTimeLocal(EncargoDia::FIN_MENSUAL_DOS);
            $date_range2 = new DatePeriod($oInicio2, $interval, $oFin2);
            $oInicio3 = new DateTimeLocal(EncargoDia::INICIO_MENSUAL_TRES);
            $oFin3 = new DateTimeLocal(EncargoDia::FIN_MENSUAL_TRES);
            $date_range3 = new DatePeriod($oInicio3, $interval, $oFin3);
            $interval3=New DateInterval(EncargoDia::INTERVAL_MENSUAL);
        }
        $a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
        foreach ($date_range as $date) {
            $num_dia = $date->format('Y-m-d');
            $dia_week = $date->format('N');
            $dia_week_sacd[$num_dia] = $date->format('N');
            $dia_mes = $date->format('d');
            $nom_dia = $a_dias_semana[$dia_week].' '.intdiv(date_diff($date, $oInicio)->format('%a'),7)+1;

            $columns_cuadricula[] = [
                "id" => "$num_dia", 
                "name" => "$nom_dia", 
                "field" => "$num_dia", 
                "width" => 80, 
                "cssClass" => "cell-title"
            ];
            $columns_sacd[] = [
                "id" => "$num_dia", 
                "name" => "$nom_dia", 
                "field" => "$num_dia", 
                "width" => 80, 
                "cssClass" => "cell-title"];
        }
        break;
    case EncargoDia::PLAN_DE_MISAS:
        $oInicio = new DateTimeLocal($Qempiezamin_rep);
        $oFin = new DateTimeLocal($Qempiezamax_rep);
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($oInicio, $interval, $oFin);
        foreach ($date_range as $date) {
            $num_dia = $date->format('Y-m-d');
            $dia_week = $date->format('N');
            $dia_week_sacd[$num_dia] = $date->format('N');
            $dia_mes = $date->format('d');
            $nom_dia=$a_dias_semana_breve[$dia_week].' '.$dia_mes;
            $columns_cuadricula[] = [
                "id" => "$num_dia", 
                "name" => "$nom_dia", 
                "field" => "$num_dia", 
                "width" => 80, 
                "cssClass" => "cell-title"
            ];
            $columns_sacd[] = [
                "id" => "$num_dia", 
                "name" => "$nom_dia", 
                "field" => "$num_dia", 
                "width" => 80, 
                "cssClass" => "cell-title"
            ];

        }
        break;
}   




$data_cuadricula = [];

$oGesEncargoTipo = new GestorEncargoTipo();

$grupo = '8...';
//if (!empty($grupo)) {
$aWhere = [];
$aOperador = [];
$aWhere['id_tipo_enc'] = '^' . $grupo;
$aOperador['id_tipo_enc'] = '~';
$oGesEncargoTipo = new GestorEncargoTipo();
$cEncargoTipos = $oGesEncargoTipo->getEncargoTipos($aWhere, $aOperador);


$posibles_encargo_tipo = [];
foreach ($cEncargoTipos as $oEncargoTipo) {
    if ($oEncargoTipo->getId_tipo_enc()>=8100)
        $a_tipo_enc[] = $oEncargoTipo->getId_tipo_enc();
}

$EncargosZona = new EncargosZona($Qid_zona, $oInicio, $oFin, $Qorden);
$EncargosZona->setATipoEnc($a_tipo_enc);
$cEncargosZona = $EncargosZona->getEncargos();
foreach ($cEncargosZona as $oEncargo) {
    $id_enc = $oEncargo->getId_enc();
    $desc_enc = $oEncargo->getDesc_enc();
//    echo $id_enc.$desc_enc.'<br>';
    $data_cols = [];
    $meta_dia = [];
    if (($QTipoPlantilla == EncargoDia::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantilla == EncargoDia::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantilla == EncargoDia::PLANTILLA_MENSUAL_TRES)) {
        $data_cols2 = [];
        $meta_dia2 = [];
        $data_cols3= [];
        $meta_dia3 = [];        
    }
    foreach ($date_range as $date) {
//        $d++;
        $num_dia = $date->format('Y-m-d');
        $nom_dia = $date->format('D');
//echo $num_dia.'<br>';
        $data_cols["$num_dia"] = " -- ";

        $meta_dia["$num_dia"] = [
            "uuid_item" => "",
            "color" => "",
            "key" => '',
            "tstart" => '',
            "tend" => '',
            "observ" => '',
            "id_enc" => $id_enc,
            "dia" => $num_dia,
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
            $oEncargoDia = $cEncargosDia[0];
            $id_nom = $oEncargoDia->getId_nom();
            $oEncargoDia = $cEncargosDia[1];
            $id_nom = $oEncargoDia->getId_nom();
            exit(_("sólo debería haber uno").'-'.$inicio_dia.'-'.$fin_dia.'-'.$id_enc);
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
                "dia" => $num_dia,
            ];
            // añadir '*' si tiene observaciones
            $iniciales .= " ".$hora_ini;
            $iniciales .= empty($oEncargoDia->getObserv())? '' : '*';
            $data_cols["$num_dia"] = $iniciales;
        }

        if (($QTipoPlantilla == EncargoDia::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantilla == EncargoDia::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantilla == EncargoDia::PLANTILLA_MENSUAL_TRES)) {
            $data_cols2["$num_dia"] = " -- ";
            $data_cols3["$num_dia"] = " -- ";

            $date2=new DateTime($num_dia);
            $date2 -> add($interval3);
            $num_dia2 = $date2->format('Y-m-d');
            $date3=new DateTime($num_dia2);
            $date3 -> add($interval3);
            $num_dia3 = $date3->format('Y-m-d');
            $meta_dia2["$num_dia"] = [
                "uuid_item" => "",
                "color" => "",
                "key" => '',
                "tstart" => '',
                "tend" => '',
                "observ" => '',
                "id_enc" => $id_enc,
                "dia" => $num_dia2,
            ];
            $meta_dia3["$num_dia"] = [
                "uuid_item" => "",
                "color" => "",
                "key" => '',
                "tstart" => '',
                "tend" => '',
                "observ" => '',
                "id_enc" => $id_enc,
                "dia" => $num_dia3,
            ];

            // sobreescribir los que tengo datos:
                $inicio_dia2 = $num_dia2.' 00:00:00';
                $fin_dia2 = $num_dia2.' 23:59:59';

                $inicio_dia3 = $num_dia3.' 00:00:00';
                $fin_dia3 = $num_dia3.' 23:59:59';
                $aWhere = [
                'id_enc' => $id_enc,
                'tstart' => "'$inicio_dia2', '$fin_dia2'",
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

                $meta_dia2["$num_dia"] = [
                    "uuid_item" => $oEncargoDia->getUuid_item()->value(),
                    "color" => $color,
                    "key" => "$id_nom#$iniciales",
                    "tstart" => $oEncargoDia->getTstart()->getHora(),
                    "tend" => $oEncargoDia->getTend()->getHora(),
                    "observ" => $oEncargoDia->getObserv(),
                    "id_enc" => $id_enc,
                    "dia" => $num_dia2,
                ];
                // añadir '*' si tiene observaciones
                $iniciales .= " ".$hora_ini;
                $iniciales .= empty($oEncargoDia->getObserv())? '' : '*';
                $data_cols2["$num_dia"] = $iniciales;
            }

            $aWhere = [
                'id_enc' => $id_enc,
                'tstart' => "'$inicio_dia3', '$fin_dia3'",
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

                $meta_dia3["$num_dia"] = [
                    "uuid_item" => $oEncargoDia->getUuid_item()->value(),
                    "color" => $color,
                    "key" => "$id_nom#$iniciales",
                    "tstart" => $oEncargoDia->getTstart()->getHora(),
                    "tend" => $oEncargoDia->getTend()->getHora(),
                    "observ" => $oEncargoDia->getObserv(),
                    "id_enc" => $id_enc,
                    "dia" => $num_dia3,
                ];
                // añadir '*' si tiene observaciones
                $iniciales .= " ".$hora_ini;
                $iniciales .= empty($oEncargoDia->getObserv())? '' : '*';
                $data_cols3["$num_dia"] = $iniciales;
            }
        }
    }

    $data_cols["encargo"] = $desc_enc;
    $data_cols["meta"] = $meta_dia;
    // añado una columna 'meta' con metadatos, invisible, porque no está
    // en la definición de columns
    $data_cuadricula[] = $data_cols;

    if (($QTipoPlantilla == EncargoDia::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantilla == EncargoDia::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantilla == EncargoDia::PLANTILLA_MENSUAL_TRES)) {
        $data_cols2["encargo"] = '';
        $data_cols2["meta"] = $meta_dia2;     
        $data_cuadricula[] = $data_cols2;
        $data_cols3["encargo"] = '';
        $data_cols3["meta"] = $meta_dia3;          
        $data_cuadricula[] = $data_cols3;
    }
}

$data_sacd = [];
$aWhere = [];
$aWhere['id_zona'] = $Qid_zona;
$aOperador = array();
$GesZonasSacd = new GestorZonaSacd();
$cZonaSacd = $GesZonasSacd->getZonasSacds($aWhere, $aOperador);
foreach ($cZonaSacd as $oZonaSacd) {
    $data_cols = [];
    $id_nom = $oZonaSacd->getId_nom();
//    $nombre_sacd=nombre_sacd($id_nom);
    $InicialesSacd = new InicialesSacd();
    $nombre_sacd=$InicialesSacd->nombre_sacd($id_nom);
    $data_cols['sacerdote']=$nombre_sacd;
//    echo $nombre_sacd.'<br>';
    $esta_en_zona=array('', $oZonaSacd->getDw1(),$oZonaSacd->getDw2(),$oZonaSacd->getDw3(),$oZonaSacd->getDw4(),$oZonaSacd->getDw5(),$oZonaSacd->getDw6(),$oZonaSacd->getDw7());
    foreach ($date_range as $date) {
        $num_dia = $date->format('Y-m-d');
        $dws = $dia_week_sacd[$num_dia];
//echo $num_dia.'-'.$dws.'='.$esta_en_zona[$dws].'<br>';
        if ($esta_en_zona[$dws]){
            $data_cols[$num_dia] = 'SI';    
        } else {
            $data_cols[$num_dia] = 'NO';    
        }
    }
    $data_sacd[]=$data_cols;
}


$json_columns_cuadricula = json_encode($columns_cuadricula);
$json_data_cuadricula = json_encode($data_cuadricula);

$json_columns_sacd = json_encode($columns_sacd);
$json_data_sacd = json_encode($data_sacd);


$oHash = new Hash();
$oHash->setCamposForm('color!dia!id_enc!key!observ!tend!tstart!uuid_item');
$array_h = $oHash->getParamAjaxEnArray();

$url_desplegable_sacd = 'apps/misas/controller/desplegable_sacd.php';
$oHash_desplegable_sacd = new Hash();
$oHash_desplegable_sacd->setUrl($url_desplegable_sacd);
$oHash_desplegable_sacd->setCamposForm('id_zona!id_sacd!seleccion!dia');
$h_desplegable_sacd = $oHash_desplegable_sacd->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'json_columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
    'json_columns_sacd' => $json_columns_sacd,
    'json_data_sacd' => $json_data_sacd,
    'url_desplegable_sacd' =>$url_desplegable_sacd,
    'h_desplegable_sacd' => $h_desplegable_sacd,
    'id_zona' => $Qid_zona,
    'array_h' => $array_h,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('ver_cuadricula_zona.html.twig', $a_campos);