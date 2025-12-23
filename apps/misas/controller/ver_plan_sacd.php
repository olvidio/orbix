<?php


// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use core\ViewTwig;
use encargossacd\model\entity\Encargo;
use misas\domain\entity\EncargoDia;
use misas\domain\repositories\EncargoDiaRepository;
use src\usuarios\application\repositories\UsuarioRepository;
use web\DateTimeLocal;
use web\Hash;
use zonassacd\model\entity\GestorZona;

//use web\Desplegable;
//use zonassacd\model\entity\GestorZonaSacd;
//use personas\model\entity\GestorPersona;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$id_usuario = ConfigGlobal::mi_id_usuario();
$UsuarioRepository = new UsuarioRepository();
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$id_sacd = $oMiUsuario->getId_pauAsString();
$id_role = $oMiUsuario->getId_role();
$GesZonas = new GestorZona();
$cZonas = $GesZonas->getZonas(array('id_nom' => $id_sacd));
$jefe_zona = (is_array($cZonas) && count($cZonas) > 0);

$Qid_sacd = (integer)filter_input(INPUT_POST, 'id_sacd');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qorden = (string)filter_input(INPUT_POST, 'orden');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

switch ($Qperiodo) {
    case "esta_semana":
        $dia_week = date('N');
        $dia_week--;
        if ($dia_week==-1){
            $dia_week=6;
        }
        $empiezamin = new DateTimeLocal(date('Y-m-d'));
        $intervalo='P'.($dia_week).'D';
        $di = new DateInterval($intervalo);
        $di->invert = 1; // intervalo negativo

        $empiezamin->add($di);
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
        $intervalo='P7D';
        $empiezamax = $empiezamin;
        $empiezamax->add(new DateInterval($intervalo));
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
        break;
    case "proxima_semana":
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
    case "este_mes":
        $este_mes = date('m');
        $anyo = date('Y');
        $empiezamin = new DateTimeLocal(date($anyo.'-'.$este_mes.'-01'));
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
        $siguiente_mes = $este_mes + 1;
        if ($siguiente_mes == 13) {
            $siguiente_mes = 1;
            $anyo++;
        }
        $empiezamax = new DateTimeLocal(date($anyo.'-'.$siguiente_mes.'-01'));
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
        break;

        
    case "proximo_mes":
        $proximo_mes = date('m') + 1;
        $anyo = date('Y');
        if ($proximo_mes == 13) {
            $proximo_mes = 1;
            $anyo++;
        }
        $empiezamin = new DateTimeLocal(date($anyo.'-'.$proximo_mes.'-01'));
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
        $siguiente_mes = $proximo_mes + 1;
        if ($siguiente_mes == 13) {
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

$a_dias_semana_breve=[1=>'L', 2=>'M', 3=>'X', 4=>'J', 5=>'V', 6=>'S', 7=>'D'];
$a_nombre_mes_breve=[1=>'Ene', 2=>'feb', 3=>'mar', 4=>'abr', 5=>'may', 6=>'jun', 7=>'jul', 8=>'ago', 9=>'sep', 10=>'oct', 11=>'nov', 12=>'dic'];


$columns_cuadricula = [
    ["id" => "dia", "name" => "Dia", "field" => "dia", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 250, "cssClass" => "cell-title"],
    ["id" => "observaciones", "name" => "Observaciones", "field" => "observaciones", "width" => 250, "cssClass" => "cell-title"],
];
echo '<TABLE>';
echo '<TR>';
//echo '<TH class="cell-title" style:"width:500px">Encargo</TH>';
echo '<TH class="cell-title" style:"width:10%">Dia</TH>';
echo '<TH class="cell-title" style:"width:30%">Encargo</TH>';
echo '<TH class="cell-title" style:"width:30%">Observaciones</TH>';

$dia_week_sacd = [];
$oInicio = new DateTimeLocal($Qempiezamin_rep);
$oFin = new DateTimeLocal($Qempiezamax_rep);

$interval = new DateInterval('P1D');
$date_range = new DatePeriod($oInicio, $interval, $oFin);

$data_cuadricula = [];

$inicio_dia = $Qempiezamin_rep.' 00:00:00';
$fin_dia = $Qempiezamax_rep.' 23:59:59';

$aWhere = [
    'id_nom' => $Qid_sacd,
    'tstart' => "'$inicio_dia', '$fin_dia'",
];
$aWhere['_ordre'] = 'tstart';
$aOperador = [
    'tstart' => 'BETWEEN',
];
$EncargoDiaRepository = new EncargoDiaRepository();
$cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);

foreach($cEncargosDia as $oEncargoDia) {
    $id_enc = $oEncargoDia->getId_enc();
    $date = $oEncargoDia->getTstart();
    $status = $oEncargoDia->getStatus();
//    $dia = $date->format('d-m-Y');
    $num_dia = $date->format('j');
    $num_mes = $date->format('n');
    $dia_week = $date->format('N');
    $dia=$a_dias_semana_breve[$dia_week].' '.$num_dia.'.'.$num_mes;
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

    $oEncargo = new Encargo($id_enc);
    $desc_enc = $oEncargo->getDesc_enc();


//echo 'jefe: '.$jefe_zona.' s: '.$status.'<br>';
    if ($jefe_zona || ($status==EncargoDia::STATUS_COMUNICADO_SACD) || ($status==EncargoDia::STATUS_COMUNICADO_CTR))
    {
        $data_cols["dia"] = $dia_y_hora;
        $data_cols["observaciones"] = $observ;
        $data_cols["encargo"] = $desc_enc;
        $data_cuadricula[] = $data_cols;
        echo '</TR>';
        echo '<TR><TD>'.$dia_y_hora.'</TD>';
        echo '<TD>'.$desc_enc.'</TD>';
        echo '<TD>'.$observ.'</TD>';
    }
}

echo '</TR>';
echo '</TABLE>';


$json_columns_cuadricula = json_encode($columns_cuadricula);
$json_data_cuadricula = json_encode($data_cuadricula);


$a_campos = ['oPosicion' => $oPosicion,
    'json_columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
];

//$oView = new ViewTwig('misas/controller');
//echo $oView->render('ver_plan_sacd.html.twig', $a_campos);