<?php


// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\EncargoConstants;
use misas\domain\repositories\EncargoDiaRepository;
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
/*
if ($Qseleccion & 8) { 
    $a_Clases = [];
    $a_Clases[] = array('clase' => 'PersonaEx', 'get' => 'getPersonasEx');
    $aWhere = [];
    $aOperador = [];
    $aWhere['sacd'] = 't';
    $aWhere['situacion'] = 'A';
    $aWhere['_ordre'] = 'apellido1,apellido2,nom';
    $GesPersonas = new GestorPersona();
    $GesPersonas->setClases($a_Clases);
    $cPersonas = $GesPersonas->getPersonas($aWhere, $aOperador);
    foreach ($cPersonas as $oPersona) {
        $id_nom = $oPersona->getId_nom();
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
*/

$columns_cuadricula = [
    ["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 150, "cssClass" => "cell-title"],
];

switch ($QTipoPlantilla) {
    case 's':
        $oInicio = new DateTimeLocal('2001-01-01');
        $oFin = new DateTimeLocal('2001-01-08');
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($oInicio, $interval, $oFin);
        $a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
        foreach ($date_range as $date) {
            $num_dia = $date->format('Y-m-d');
            //$nom_dia = $date->format('D');
            $dia_week = $date->format('N');
            //$dia_mes = $date->format('d');
            $nom_dia = $a_dias_semana[$dia_week];
        
            $columns_cuadricula[] =
                ["id" => "$num_dia", "name" => "$nom_dia", "field" => "$num_dia", "width" => 80, "cssClass" => "cell-title"];
        }
        break;
    case 'd':
        $oInicio = new DateTimeLocal('2001-10-01');
        $oFin = new DateTimeLocal('2001-10-12');
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($oInicio, $interval, $oFin);
        $a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
        foreach ($date_range as $date) {
            $num_dia = $date->format('Y-m-d');
            //$nom_dia = $date->format('D');
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
    case 'm':
        $oInicio = new DateTimeLocal('2002-04-01');
        $oFin = new DateTimeLocal('2002-05-06');
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($oInicio, $interval, $oFin);
        $a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
        foreach ($date_range as $date) {
            $num_dia = $date->format('Y-m-d');
            //$nom_dia = $date->format('D');
            $dia_week = $date->format('N');
            $dia_mes = $date->format('d');
//            $nom_dia = $a_dias_semana[$dia_week].' '.strval(intdiv(($dia_mes-1),7)+1);
//            $nom_dia = $a_dias_semana[$dia_week].' '.strval(intdiv(date_diff($date, $oInicio),7)+1);
            $nom_dia = $a_dias_semana[$dia_week].' '.intdiv(date_diff($date, $oInicio)->format('%a'),7)+1;
            $columns_cuadricula[] =
                ["id" => "$num_dia", "name" => "$nom_dia", "field" => "$num_dia", "width" => 80, "cssClass" => "cell-title"];
        }
        break;
    
    }
//$oInicio = new DateTimeLocal('2001-01-01');
//$oFin = new DateTimeLocal('2001-01-08');




$data_cuadricula = [];
// encargos de misa (8010) para la zona
$a_tipo_enc = [8010, 8011];
$EncargosZona = new EncargosZona($Qid_zona, $oInicio, $oFin);
$EncargosZona->setATipoEnc($a_tipo_enc);
$cEncargosZona = $EncargosZona->getEncargos();
$e = 0;
foreach ($cEncargosZona as $oEncargo) {
    $e++;
    $id_enc = $oEncargo->getId_enc();
    $desc_enc = $oEncargo->getDesc_enc();
    $d = 0;
    $data_cols = [];
    $meta_dia = [];
    foreach ($date_range as $date) {
        $d++;
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
//            $iniciales = $a_iniciales[$id_nom];
            $iniciales = iniciales($id_nom);
//              $iniciales = $id_nom;
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

$url_desplegable_sacd = 'apps/misas/controller/desplegable_sacd.php';
$oHash_desplegable_sacd = new Hash();
$oHash_desplegable_sacd->setCamposForm('id_zona!id_sacd');
$h_desplegable_sacd = $oHash_desplegable_sacd->getParamAjaxEnArray();

$a_campos = ['oPosicion' => $oPosicion,
    'json_columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
    'url_desplegable_sacd' =>$url_desplegable_sacd,
    'h_desplegable_sacd' => $h_desplegable_sacd,
    'id_zona' => $Qid_zona,
    'array_h' => $array_h,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('ver_cuadricula_zona.html.twig', $a_campos);
