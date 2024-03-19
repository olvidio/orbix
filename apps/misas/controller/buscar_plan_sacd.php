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

$Qid_sacd = (integer)filter_input(INPUT_POST, 'id_sacd');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qorden = (string)filter_input(INPUT_POST, 'orden');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin_rep=str_replace('/','-',$Qempiezamin);
$Qempiezamax_rep=str_replace('/','-',$Qempiezamax);

$a_dias_semana_breve=[1=>'L', 2=>'M', 3=>'X', 4=>'J', 5=>'V', 6=>'S', 7=>'D'];
$a_nombre_mes_breve=[1=>'Ene', 2=>'feb', 3=>'mar', 4=>'abr', 5=>'may', 6=>'jun', 7=>'jul', 8=>'ago', 9=>'sep', 10=>'oct', 11=>'nov', 12=>'dic'];


$columns_cuadricula = [
    ["id" => "dia", "name" => "Dia", "field" => "dia", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 250, "cssClass" => "cell-title"],
    ["id" => "observaciones", "name" => "Observaciones", "field" => "observaciones", "width" => 250, "cssClass" => "cell-title"],
];

$dia_week_sacd = [];
if ($Qempiezamin!='') {
    $oInicio = new DateTimeLocal($Qempiezamin_rep);
}
if ($Qempiezamax!='') {
    $oFin = new DateTimeLocal($Qempiezamax_rep);
}
$interval = new DateInterval('P1D');
$date_range = new DatePeriod($oInicio, $interval, $oFin);

$data_cuadricula = [];

/*$oGesEncargoTipo = new GestorEncargoTipo();

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
*/

/*$EncargosZona = new EncargosZona($Qid_zona, $oInicio, $oFin, $Qorden);
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

        // sobreescribir los que tengo datos:*/
$inicio_dia = '2024-3-01 00:00:00';
$fin_dia = '2024-3-31 23:59:59';
$aWhere = [
    'id_nom' => $Qid_sacd,
    'tstart' => "'$inicio_dia', '$fin_dia'",
];
$aOperador = [
    'tstart' => 'BETWEEN',
];
$EncargoDiaRepository = new EncargoDiaRepository();
$cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);

foreach($cEncargosDia as $oEncargoDia) {
    $id_enc = $oEncargoDia->getId_enc();
    $dia = $oEncargoDia->getTstart()->format('d-m-Y');
    $hora_ini = $oEncargoDia->getTstart()->format('H:i');
    $hora_fin = $oEncargoDia->getTend()->format('H:i');
    if ($hora_ini=='00:00')
        $hora_ini='';
    if ($hora_fin=='00:00')
        $hora_fin='';
    $observ = $oEncargoDia->getObserv();
    $dia_y_hora=$dia;
    if ($hora_ini!='') {
        $dia_y_hora .= ' '.$hora_ini;
        if ($hora_fin!='') {
            $dia_y_hora .= '-'.$hora_fin;
        }
    }

    $data_cols["dia"] = $dia_y_hora;
    $data_cols["observaciones"] = $observ;
//    $data_cols["encargo"] = $desc_enc;
    $data_cols["encargo"] = $id_enc;
    echo $id_enc.$dia_y_hora.$observ.'<br>';

    $data_cuadricula[] = $data_cols;
}



$json_columns_cuadricula = json_encode($columns_cuadricula);
$json_data_cuadricula = json_encode($data_cuadricula);


$a_campos = ['oPosicion' => $oPosicion,
    'json_columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('ver_cuadricula_zona.html.twig', $a_campos);