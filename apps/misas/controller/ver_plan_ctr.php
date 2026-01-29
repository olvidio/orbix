<?php


use core\ConfigGlobal;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\entity\Ubi;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
$id_usuario = ConfigGlobal::mi_id_usuario();
$UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$id_sacd = $oMiUsuario->getCsvIdPauAsString();
$id_role = $oMiUsuario->getId_role();
$ZonasRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
$cZonas = $ZonasRepository->getZonas(array('id_nom' => $id_sacd));
$jefe_zona = (is_array($cZonas) && count($cZonas) > 0);


$RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
$aRoles = $RoleRepository->getArrayRoles();
//echo $aRoles[$id_role];
$role = '';

if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p-sacd')) {
    $role = 'sacd';
}

if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'Centro sv' || $aRoles[$id_role] === 'Centro sf')) {
    $role = 'ctr';
}

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qorden = (string)filter_input(INPUT_POST, 'orden');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

//echo 'id_ubi: ' . $Qid_ubi . '<br>';
switch ($Qperiodo) {
    case "esta_semana":
        $dia_week = (int)date('N');
        $dia_week--;
        if ($dia_week === -1) {
            $dia_week = 6;
        }
        $empiezamin = new DateTimeLocal(date('Y-m-d'));
        $intervalo = 'P' . ($dia_week) . 'D';
        $di = new DateInterval($intervalo);
        $di->invert = 1; // intervalo negativo

        $empiezamin->add($di);
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
        $intervalo = 'P7D';
        $empiezamax = $empiezamin;
        $empiezamax->add(new DateInterval($intervalo));
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
        break;
    case "proxima_semana":
        $dia_week = date('N');
        $empiezamin = new DateTimeLocal(date('Y-m-d'));
        $intervalo = 'P' . (8 - $dia_week) . 'D';
        $empiezamin->add(new DateInterval($intervalo));
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
        $intervalo = 'P7D';
        $empiezamax = $empiezamin;
        $empiezamax->add(new DateInterval($intervalo));
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
        break;
    case "este_mes":
        $este_mes = (int)date('m');
        $anyo = (int)date('Y');
        $empiezamin = new DateTimeLocal(date($anyo . '-' . $este_mes . '-01'));
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
        $siguiente_mes = (int)$este_mes + 1;
        if ($siguiente_mes == 13) {
            $siguiente_mes = 1;
            $anyo++;
        }
        $empiezamax = new DateTimeLocal(date($anyo . '-' . $siguiente_mes . '-01'));
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
        break;


    case "proximo_mes":
        $proximo_mes = (int)date('m') + 1;
        $anyo = (int)date('Y');
        if ($proximo_mes == 13) {
            $proximo_mes = 1;
            $anyo++;
        }
        $empiezamin = new DateTimeLocal(date($anyo . '-' . $proximo_mes . '-01'));
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
        $siguiente_mes = $proximo_mes + 1;
        if ($siguiente_mes === 13) {
            $siguiente_mes = 1;
            $anyo++;
        }
        $empiezamax = new DateTimeLocal(date($anyo . '-' . $siguiente_mes . '-01'));
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
        break;
    default:
        $oInicio = DateTimeLocal::createFromLocal($Qempiezamin);
        $oFin = DateTimeLocal::createFromLocal($Qempiezamax);
        $Qempiezamin_rep = $oInicio->format('Y-m-d');
        $Qempiezamax_rep = $oFin->format('Y-m-d');
}

$a_dias_semana_breve = [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V', 6 => 'S', 7 => 'D'];
$a_nombre_mes_breve = [1 => 'Ene', 2 => 'feb', 3 => 'mar', 4 => 'abr', 5 => 'may', 6 => 'jun', 7 => 'jul', 8 => 'ago', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic'];


$columns_cuadricula = [
    ["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 250, "cssClass" => "cell-title"],
];
echo '<TABLE style="width: 100%">';
echo '<TR>';
//echo '<TH class="cell-title" style:"width:500px">Encargo</TH>';
echo '<TH class="cell-title" style:"width:10%">Encargo</TH>';

$dia_week_sacd = [];
$oInicio = new DateTimeLocal($Qempiezamin_rep);
$oFin = new DateTimeLocal($Qempiezamax_rep);

$interval = new DateInterval('P1D');
$date_range = new DatePeriod($oInicio, $interval, $oFin, 0);

$inicio_dia = $Qempiezamin_rep . ' 00:00:00';
$fin_dia = $Qempiezamax_rep . ' 23:59:59';

foreach ($date_range as $date) {
    $id_dia = $date->format('Y-m-d');
    $num_dia = $date->format('j');
    $num_mes = $date->format('n');
    $dia_week = $date->format('N');
    $dia_week_sacd[$id_dia] = $date->format('N');
    $nom_dia = $a_dias_semana_breve[$dia_week] . ' ' . $num_dia . '.' . $num_mes;
    $nom_dia2 = $a_dias_semana_breve[$dia_week] . '<br>' . $num_dia . '.' . $num_mes;
    //    $nom_dia = $a_dias_semana_breve[$dia_week].$num_dia.$a_nombre_mes_breve[$num_mes];

    $columns_cuadricula[] = [
        "id" => "$id_dia",
        "name" => "$nom_dia",
        "field" => "$id_dia",
        "width" => 80,
        "cssClass" => "cell-title"
    ];
    echo '<TH class=cell-title style:"width:60px">' . $nom_dia2 . '</TH>';
}
$data_cuadricula = [];

$EncargoCtrRepository = $GLOBALS['container']->get(EncargoCtrRepositoryInterface::class);
$cEncargosCtr = $EncargoCtrRepository->getEncargosCentro($Qid_ubi);
$EncargoTipoRepository = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);
$EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
$InicialesSacdService = $GLOBALS['container']->get(InicialesSacdService::class);
foreach ($cEncargosCtr as $oEncargoCtr) {
    $id_enc = $oEncargoCtr->getId_enc();
    $oEncargo = $EncargoRepository->findBYId($id_enc);
    $desc_enc = $oEncargo->getDesc_enc();
    $id_ubi = $oEncargo->getId_ubi();
    $id_tipo_enc = $oEncargo->getId_tipo_enc();
    $desc_lugar = $oEncargo->getDesc_lugar();
    $idioma_enc = $oEncargo->getIdioma_enc();
    $orden = $oEncargo->getOrden();
    $prioridad = $oEncargo->getPrioridad();
    $observ = $oEncargo->getObserv();

    $data_cols["encargo"] = $desc_enc;
    echo '</TR>';
    echo '<TR><TD>' . $desc_enc . '</TD>';

    if (!empty($id_ubi)) {
        $oUbi = Ubi::newUbi($id_ubi);
        $nombre_ubi = $oUbi->getNombre_ubi();
    } else {
        $nombre_ubi = '';
    }

    if (!empty($id_tipo_enc)) {
        $oEncargoTipo = $EncargoTipoRepository->findById($id_tipo_enc);
        $tipo_enc = $oEncargoTipo->getTipo_enc();
    } else {
        $tipo_enc = '';
    }

    foreach ($date_range as $date) {
        $iniciales = ' -- ';
        $status = EncargoDiaStatus::STATUS_COMUNICADO_CTR;

        $id_dia = $date->format('Y-m-d');

        $inicio_dia = $id_dia . ' 00:00:00';
        $fin_dia = $id_dia . ' 23:59:59';

        $aWhere = [
            'id_enc' => $id_enc,
            'tstart' => "'$inicio_dia', '$fin_dia'",
        ];
        $aWhere['_ordre'] = 'tstart';
        $aOperador = [
            'tstart' => 'BETWEEN',
        ];
        $EncargoDiaRepository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
        $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);

        foreach ($cEncargosDia as $oEncargoDia) {
            $id_enc = $oEncargoDia->getId_enc();
            $id_nom = $oEncargoDia->getId_nom();
            $status = $oEncargoDia->getStatus();
            $dia = $oEncargoDia->getTstart()->format('d-m-Y');
            $hora_ini = $oEncargoDia->getTstart()->format('H:i');
            $hora_fin = $oEncargoDia->getTend()->format('H:i');
            if ($hora_ini === '00:00')
                $hora_ini = '';
            if ($hora_fin === '00:00')
                $hora_fin = '';
            $observ = $oEncargoDia->getObserv();
            $dia_y_hora = $dia;
            if ($hora_ini !== '') {
                $dia_y_hora .= ' ' . $hora_ini;
            }
            if ($hora_fin !== '') {
                $dia_y_hora .= '-' . $hora_fin;
            }
            $iniciales = $InicialesSacdService->obtenerIniciales($id_nom);
            $color = '';

            $meta_dia["$id_dia"] = [
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
            $iniciales .= " " . $hora_ini;
            $iniciales .= empty($oEncargoDia->getObserv()) ? '' : '*';
            $data_cols["$id_dia"] = $iniciales;
        }

        if (($jefe_zona) || (($role == 'ctr') && ($status == EncargoDiaStatus::STATUS_COMUNICADO_CTR)) || (($role == 'sacd') && (($status == EncargoDiaStatus::STATUS_COMUNICADO_SACD) || ($status == EncargoDiaStatus::STATUS_COMUNICADO_CTR)))) {
            echo '<TD>' . $iniciales . '</TD>';
        } else {
            if (($jefe_zona) || (($role === 'ctr')
                    && ($status === EncargoDiaStatus::STATUS_COMUNICADO_CTR)) || (($role === 'sacd')
                    && (($status === EncargoDiaStatus::STATUS_COMUNICADO_SACD) || ($status === EncargoDiaStatus::STATUS_COMUNICADO_CTR)))) {
                echo '<TD>' . $iniciales . '</TD>';
            } else {
                echo '<TD> -- </TD>';
            }

//        $data_cols["dia"] = $dia_y_hora;
//        $data_cols["observaciones"] = $observ;

//        $oEncargo = new Encargo($id_enc);
//        $desc_enc = $oEncargo->getDesc_enc();

//        $data_cols["encargo"] = $desc_enc;


        }
        $data_cuadricula[] = $data_cols;
    }

    echo '</TR>';
    echo '</TABLE>';


    $json_columns_cuadricula = json_encode($columns_cuadricula);
    $json_data_cuadricula = json_encode($data_cuadricula);


    $a_campos = ['oPosicion' => $oPosicion,
        'json_columns_cuadricula' => $json_columns_cuadricula,
        'json_data_cuadricula' => $json_data_cuadricula,
    ];
}

//$oView = new ViewTwig('misas/controller');
//echo $oView->render('ver_plan_ctr.html.twig', $a_campos);