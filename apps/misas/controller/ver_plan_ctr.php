<?php


// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\EncargoConstants;
use misas\domain\repositories\EncargoDiaRepository;
use misas\domain\repositories\EncargoCtrRepository;
use misas\domain\repositories\InicialesSacdRepository;
use misas\domain\entity\InicialesSacd;
use misas\domain\entity\EncargoDia;
use misas\model\EncargosZona;
use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\EncargoTipo;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoTipo;
//use personas\model\entity\GestorPersona;
use personas\model\entity\PersonaEx;
use personas\model\entity\PersonaSacd;
use ubis\model\entity\Ubi;
use web\DateTimeLocal;
//use web\Desplegable;
use web\Hash;
use zonassacd\model\entity\GestorZonaSacd;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
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
        if ($siguiente_mes == 12) {
            $siguiente_mes = 1;
            $anyo++;
        }
        $empiezamax = new DateTimeLocal(date($anyo.'-'.$siguiente_mes.'-01'));
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
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

$a_dias_semana_breve=[1=>'L', 2=>'M', 3=>'X', 4=>'J', 5=>'V', 6=>'S', 7=>'D'];
$a_nombre_mes_breve=[1=>'Ene', 2=>'feb', 3=>'mar', 4=>'abr', 5=>'may', 6=>'jun', 7=>'jul', 8=>'ago', 9=>'sep', 10=>'oct', 11=>'nov', 12=>'dic'];


$columns_cuadricula = [
    ["id" => "dia", "name" => "Dia", "field" => "dia", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 250, "cssClass" => "cell-title"],
    ["id" => "observaciones", "name" => "Observaciones", "field" => "observaciones", "width" => 250, "cssClass" => "cell-title"],
];

$dia_week_sacd = [];
$oInicio = new DateTimeLocal($Qempiezamin_rep);
$oFin = new DateTimeLocal($Qempiezamax_rep);

$interval = new DateInterval('P1D');
$date_range = new DatePeriod($oInicio, $interval, $oFin);

$inicio_dia = $Qempiezamin_rep.' 00:00:00';
$fin_dia = $Qempiezamax_rep.' 23:59:59';

echo 'D: '.$inicio_dia.'-'.$fin_dia.'<br>';
echo 'U: '.$Qid_ubi.'<br>';

foreach ($date_range as $date) {
    $id_dia = $date->format('Y-m-d');
    $num_dia = $date->format('j');
    $num_mes = $date->format('n');
    $dia_week = $date->format('N');
    $dia_week_sacd[$id_dia] = $date->format('N');
    $nom_dia = $a_dias_semana_breve[$dia_week].$num_dia.$a_nombre_mes_breve[$num_mes];

    $columns_cuadricula[] = [
        "id" => "$id_dia", 
        "name" => "$nom_dia", 
        "field" => "$id_dia", 
        "width" => 80, 
        "cssClass" => "cell-title"
    ];
}
$data_cuadricula = [];

$EncargoCtrRepository = new EncargoCtrRepository();
$cEncargosCtr = $EncargoCtrRepository->getEncargosCentro($Qid_ubi);
foreach ($cEncargosCtr as $oEncargoCtr) {
    $id_enc = $oEncargoCtr->getId_enc();
    echo 'E: '.$id_enc.'<br>';
    $oEncargo = new Encargo($id_enc);
    $desc_enc = $oEncargo->getDesc_enc();
    $id_ubi = $oEncargo->getId_ubi();
    echo 'U: '.$id_ubi.'<br>';
    $id_tipo_enc = $oEncargo->getId_tipo_enc();
    $desc_lugar = $oEncargo->getDesc_lugar();
    $idioma_enc = $oEncargo->getIdioma_enc();
    $orden = $oEncargo->getOrden();
    $prioridad = $oEncargo->getPrioridad();
    $observ = $oEncargo->getObserv();

    if (!empty($id_ubi)) {
        $oUbi = Ubi::newUbi($id_ubi);
        $nombre_ubi = $oUbi->getNombre_ubi();
    } else {
        $nombre_ubi = '';
    }

    if (!empty($id_tipo_enc)) {
        $oEncargoTipo = new EncargoTipo($id_tipo_enc);
        $tipo_enc = $oEncargoTipo->getTipo_enc();
    } else {
        $tipo_enc = '';
    }

    foreach ($date_range as $date) {
        $id_dia = $date->format('Y-m-d');
    
        $inicio_dia = $id_dia.' 00:00:00';
        $fin_dia = $id_dia.' 23:59:59';

        echo 'I:'.$inicio_dia.'<br>';
        $aWhere = [
            'id_enc' => $id_enc,
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

        $oEncargo = new Encargo($id_enc);
        $desc_enc = $oEncargo->getDesc_enc();

        $data_cols["encargo"] = $desc_enc;

        $data_cuadricula[] = $data_cols;

    }
}


}


$json_columns_cuadricula = json_encode($columns_cuadricula);
$json_data_cuadricula = json_encode($data_cuadricula);


$a_campos = ['oPosicion' => $oPosicion,
    'json_columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('ver_plan_ctr.html.twig', $a_campos);