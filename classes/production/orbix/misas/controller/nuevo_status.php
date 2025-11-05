<?php


// INICIO Cabecera global de URL de controlador *********************************
use core\ViewTwig;
use encargossacd\model\EncargoConstants;
use encargossacd\model\entity\GestorEncargoTipo;
use misas\domain\repositories\EncargoDiaRepository;
use misas\model\EncargosZona;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use web\TiposActividades;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qestado = (integer)filter_input(INPUT_POST, 'estado');

$menos_un_dia = new DateInterval('P1D');
$menos_un_dia->invert = 1;

echo 'zona:'.$Qid_zona.' estado: '.$Qestado.' periodo '.$Qperiodo.'<br>';

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
        if ($proximo_mes == 13) {
            $proximo_mes = 1;
            $anyo++;
        }
        $empiezamin = new DateTimeLocal(date($anyo.'-'.$proximo_mes.'-01'));
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
        echo 'empieza'.$Qempiezamin_rep.'<br>';
        $siguiente_mes = $proximo_mes + 1;
        if ($siguiente_mes == 13) {
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

    //Modifico el status de los encargos de la zona en ese periodo
    $EncargoDiaRepository = new EncargoDiaRepository();
    $cEncargosaCambiar = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);
    foreach($cEncargosaCambiar as $oEncargoaCambiar) {
        $oEncargoaCambiar->setStatus($Qestado);
        echo $Qestado.'----'.$oEncargoaCambiar->getUuid_item().'<br>';
        if ($EncargoDiaRepository->Guardar($oEncargoaCambiar) === FALSE) {
            $error_txt .= $EncargoDiaRepository->getErrorTxt();
        }  
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

$oView = new ViewTwig('misas/controller');
echo $oView->render('ver_cuadricula_zona.html.twig', $a_campos);
