<?php


// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\EncargoConstants;
use misas\domain\repositories\EncargoDiaRepository;
use misas\domain\entity\EncargoDia;
use misas\model\EncargosZona;
use personas\model\entity\PersonaSacd;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use zonassacd\model\entity\GestorZonaSacd;
use personas\model\entity\GestorPersona;
use personas\model\entity\PersonaEx;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

function iniciales($id_nom) {
    if ($id_nom>0) {
        $PersonaSacd = new PersonaSacd($id_nom);
        // iniciales
        $nom = mb_substr($PersonaSacd->getNom(), 0, 1);
        $ap1 = mb_substr($PersonaSacd->getApellido1(), 0, 1);
        $ap2 = mb_substr($PersonaSacd->getApellido2(), 0, 1);
    } else {
        $PersonaEx = new PersonaEx($id_nom);
        $sacdEx = $PersonaEx->getNombreApellidos();
        // iniciales
        $nom = mb_substr($PersonaEx->getNom(), 0, 1);
        $ap1 = mb_substr($PersonaEx->getApellido1(), 0, 1);
        $ap2 = mb_substr($PersonaEx->getApellido2(), 0, 1);
    }
    $iniciales = strtoupper($nom . $ap1 . $ap2);
    return $iniciales;
}

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$QTipoPlantilla = (string)filter_input(INPUT_POST, 'tipo_plantilla');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin_rep=str_replace('/','-',$Qempiezamin);
$Qempiezamax_rep=str_replace('/','-',$Qempiezamax);

$columns_cuadricula = [
    ["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 150, "cssClass" => "cell-title"],
];

switch ($QTipoPlantilla) {
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
            $nom_dia = $a_dias_semana[$dia_week];
        
            $columns_cuadricula[] =
                ["id" => "$num_dia", "name" => "$nom_dia", "field" => "$num_dia", "width" => 80, "cssClass" => "cell-title"];
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
        }
        $a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
        foreach ($date_range as $date) {
            $num_dia = $date->format('Y-m-d');
            $dia_week = $date->format('N');
            $dia_mes = $date->format('d');
            if ($dia_mes<7)
                $nom_dia = $a_dias_semana[$dia_week];
            else
                $nom_dia = 'domingo '.strval($dia_mes-6);
        
            $columns_cuadricula[] =
                ["id" => "$num_dia", "name" => "$nom_dia", "field" => "$num_dia", "width" => 80, "cssClass" => "cell-title"];
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
        }
        $a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
        foreach ($date_range as $date) {
            $num_dia = $date->format('Y-m-d');
            $dia_week = $date->format('N');
            $dia_mes = $date->format('d');
            $nom_dia = $a_dias_semana[$dia_week].' '.intdiv(date_diff($date, $oInicio)->format('%a'),7)+1;

            $columns_cuadricula[] =
                ["id" => "$num_dia", "name" => "$nom_dia", "field" => "$num_dia", "width" => 80, "cssClass" => "cell-title"];
        }
        break;
    
    }

if ($Qempiezamin!='')
    $oInicio = $Qempiezamin_rep;
if ($Qempiezamax!='')
    $oInicio = $Qempiezamax_rep;

$data_cuadricula = [];
// encargos de misa (8010) para la zona
$a_tipo_enc = [8100, 8101, 8103, 8200, 8203, 8300, 8302, 8303];
$EncargosZona = new EncargosZona($Qid_zona, $oInicio, $oFin);
$EncargosZona->setATipoEnc($a_tipo_enc);
$cEncargosZona = $EncargosZona->getEncargos();
//$e = 0;
foreach ($cEncargosZona as $oEncargo) {
//    $e++;
    $id_enc = $oEncargo->getId_enc();
    $desc_enc = $oEncargo->getDesc_enc();
//    $d = 0;
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
            $iniciales .= " ".$hora_ini;
            $iniciales .= empty($oEncargoDia->getObserv())? '' : '*';
            $data_cols["$num_dia"] = $iniciales;
        }

        if (($QTipoPlantilla == EncargoDia::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantilla == EncargoDia::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantilla == EncargoDia::PLANTILLA_MENSUAL_TRES)) {
            $data_cols2["$num_dia"] = " -- ";
            $data_cols3["$num_dia"] = " -- ";

            $meta_dia2["$num_dia"] = [
                "uuid_item" => "",
                "color" => "",
                "key" => '',
                "tstart" => '',
                "tend" => '',
                "observ" => '',
                "id_enc" => $id_enc,
            ];
            $meta_dia3["$num_dia"] = [
                "uuid_item" => "",
                "color" => "",
                "key" => '',
                "tstart" => '',
                "tend" => '',
                "observ" => '',
                "id_enc" => $id_enc,
            ];
            echo 'date:'.$date->format('Y-m-d').'<br>';
            $date2=$date;
            echo 'date2:'.$date2->format('Y-m-d').'<br>';
            $date2 -> add($interval3);
            echo 'date2 add:'.$date2->format('Y-m-d').'<br>';
            $date3=$date2;
            echo 'date3:'.$date2->format('Y-m-d').'<br>';
            $date3 -> add($interval3);
            echo 'date3 add:'.$date2->format('Y-m-d').'<br>';
            $num_dia2 = $date2->format('Y-m-d');
            $num_dia3 = $date3->format('Y-m-d');
            echo $num_dia.'-'.$num_dia2.'-'.$num_dia3.'<br>';

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
                $iniciales = iniciales($id_nom);
                $color = '';

                $meta_dia2["$num_dia"] = [
                    "uuid_item" => $oEncargoDia->getUuid_item()->value(),
                    "color" => $color,
                    "key" => "$id_nom#$iniciales",
                    "tstart" => $oEncargoDia->getTstart()->getHora(),
                    "tend" => $oEncargoDia->getTend()->getHora(),
                    "observ" => $oEncargoDia->getObserv(),
                    "id_enc" => $id_enc,
                ];
                // añadir '*' si tiene observaciones
                $iniciales .= " ".$hora_ini;
                $iniciales .= empty($oEncargoDia->getObserv())? '' : '*';
                $data_cols2["$num_dia"] = $iniciales;
            }

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
                $iniciales = iniciales($id_nom);
                $color = '';

                $meta_dia3["$num_dia"] = [
                    "uuid_item" => $oEncargoDia->getUuid_item()->value(),
                    "color" => $color,
                    "key" => "$id_nom#$iniciales",
                    "tstart" => $oEncargoDia->getTstart()->getHora(),
                    "tend" => $oEncargoDia->getTend()->getHora(),
                    "observ" => $oEncargoDia->getObserv(),
                    "id_enc" => $id_enc,
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
        $data_cols["encargo"] = '';
        $data_cols["meta"] = $meta_dia2;          
        $data_cuadricula[] = $data_cols;
        $data_cols["encargo"] = '';
        $data_cols["meta"] = $meta_dia3;          
        $data_cuadricula[] = $data_cols;
    }
}

$json_columns_cuadricula = json_encode($columns_cuadricula);
$json_data_cuadricula = json_encode($data_cuadricula);

$oHash = new Hash();
$oHash->setCamposForm('color!dia!id_enc!key!observ!tend!tstart!uuid_item');
$array_h = $oHash->getParamAjaxEnArray();

$url_desplegable_sacd = 'apps/misas/controller/desplegable_sacd.php';
$oHash_desplegable_sacd = new Hash();
$oHash_desplegable_sacd->setUrl($url_desplegable_sacd);
//$oHash_desplegable_sacd->setCamposForm('id_zona');
//$oHash_desplegable_sacd->setCamposForm('id_zona!id_sacd');
$oHash_desplegable_sacd->setCamposForm('id_zona!id_sacd!seleccion');
//$oHash_desplegable_sacd->setCamposNo('seleccion');
$h_desplegable_sacd = $oHash_desplegable_sacd->linkSinVal();

$a_iniciales = [];
$Qseleccion = 2;

if ($Qseleccion & 2) {
    $gesZonaSacd = new GestorZonaSacd();
    $a_Id_nom = $gesZonaSacd->getSacdsZona($Qid_zona);
    
    foreach ($a_Id_nom as $id_nom) {
        $PersonaSacd = new PersonaSacd($id_nom);
        $sacd = $PersonaSacd->getNombreApellidos();
        // iniciales
        $nom = mb_substr($PersonaSacd->getNom(), 0, 1);
        $ap1 = mb_substr($PersonaSacd->getApellido1(), 0, 1);
        $ap2 = mb_substr($PersonaSacd->getApellido2(), 0, 1);
        $iniciales = strtoupper($nom . $ap1 . $ap2);
    
        $a_iniciales[$id_nom] = $iniciales;
    
        $key = $id_nom . '#' . $iniciales;
    
        $a_sacd[$key] = $sacd ?? '?';
    }
}
$oDesplSacd = new Desplegable();
$oDesplSacd->setNombre('id_sacd');
$oDesplSacd->setOpciones($a_sacd);
$oDesplSacd->setBlanco(TRUE);

$a_campos = ['oPosicion' => $oPosicion,
    'json_columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
    'oDesplSacd' => $oDesplSacd,
    'url_desplegable_sacd' =>$url_desplegable_sacd,
    'h_desplegable_sacd' => $h_desplegable_sacd,
    'id_zona' => $Qid_zona,
    'array_h' => $array_h,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('ver_cuadricula_zona.html.twig', $a_campos);