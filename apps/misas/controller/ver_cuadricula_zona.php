<?php

// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use misas\domain\entity\EncargoDia;
use misas\domain\entity\InicialesSacd;
use misas\domain\repositories\EncargoDiaRepositoryInterface;
use misas\model\EncargosZona;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\EncargoConstants;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\entity\Preferencia;
use src\usuarios\domain\value_objects\TipoPreferencia;
use src\usuarios\domain\value_objects\ValorPreferencia;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use web\DateTimeLocal;
use web\Hash;
use web\TiposActividades;

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
$Qfila = (integer)filter_input(INPUT_POST, 'fila');
$Qcolumna = (integer)filter_input(INPUT_POST, 'columna');
$Qseleccion = (integer)filter_input(INPUT_POST, 'seleccion');

$un_dia = new DateInterval('P1D');

if ($QTipoPlantilla !== 'p') {
    $id_usuario = ConfigGlobal::mi_id_usuario();
    $PreferenciaRepository = $GLOBALS['container']->get(PreferenciaRepositoryInterface::class);
    $oPreferencia = $PreferenciaRepository->findById($id_usuario, 'ultima_plantilla');
    if ($oPreferencia === null) {
        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipoVo(new TipoPreferencia('ultima_plantilla'));
    }

    $oPreferencia->setPreferenciaVo(new ValorPreferencia($QTipoPlantilla));
    if ($PreferenciaRepository->Guardar($oPreferencia) === false) {
        echo _("hay un error, no se ha guardado");
        echo "\n" . $PreferenciaRepository->getErrorTxt();
    }
}
?>

    <STYLE>
        .verdeclaro {
            background-color: #d1e7dd;
        }

        .verde {
            background-color: #00ff00;
        }

        .rojoclaro {
            background-color: #f8d7da;
        }

        .rojo {
            background-color: #ff0000;
        }

        .amarilloclaro {
            background-color: #ffffaa;
        }

        .amarillo {
            background-color: #ffff00;
        }

        .violetaclaro {
            background-color: #e8a0e8;
        }

        .azulclaro2 {
            background-color: #80e0e0;
        }

        .azulclaro {
            background-color: #a0f0f0;
        }
    </STYLE>

<?php
//Busco los sacd de la zona, para señalar si en la plantilla o en plan hay alguno que no es de la zona.
$aWhere = [];
$aWhere['id_zona'] = $Qid_zona;
$aOperador = [];
$ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
$cZonaSacd = $ZonaSacdRepository->getZonasSacds($aWhere, $aOperador);
$sacd_zona = [];
foreach ($cZonaSacd as $oZonaSacd) {
    $id_nom = $oZonaSacd->getId_nom();
    $InicialesSacd = new InicialesSacd();
    $nombre_sacd = $InicialesSacd->nombre_sacd($id_nom);
    $sacd_zona[$id_nom] = $nombre_sacd;
//    echo $id_nom.'===='.$nombre_sacd.'<br>';
}


switch ($Qperiodo) {
    case "esta_semana":
        $dia_week = date('N');
        $dia_week--;
        if ($dia_week == -1) {
            $dia_week = 6;
        }
        $empiezamin = new DateTimeLocal(date('Y-m-d'));
        $intervalo = 'P' . ($dia_week) . 'D';
        $di = new DateInterval($intervalo);
        $di->invert = 1; // intervalo negativo

        $empiezamin->add($di);
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
        $intervalo = 'P6D';
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
        $intervalo = 'P6D';
        $empiezamax = $empiezamin;
        $empiezamax->add(new DateInterval($intervalo));
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
        break;
    case "este_mes":
        $este_mes = (int)date('m');
        $anyo = (int)date('Y');
        $empiezamin = new DateTimeLocal(date($anyo . '-' . $este_mes . '-01'));
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
        $siguiente_mes = $este_mes + 1;
        if ($siguiente_mes == 13) {
            $siguiente_mes = 1;
            $anyo++;
        }

        $empiezamax = new DateTimeLocal(date($anyo . '-' . $siguiente_mes . '-01'));
        $empiezamax->sub($un_dia);
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
        if ($siguiente_mes == 13) {
            $siguiente_mes = 1;
            $anyo++;
        }
        $empiezamax = new DateTimeLocal(date($anyo . '-' . $siguiente_mes . '-01'));
        $empiezamax->sub($un_dia);
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
        break;
    default:
        if ($Qempiezamin > $Qempiezamax)
            $Qempiezamax = $Qempiezamin;
        $Qempiezamin_rep = str_replace('/', '-', $Qempiezamin);
        $Qempiezamax_rep = str_replace('/', '-', $Qempiezamax);
}

if (!isset($Qorden) || ($Qorden === null))
    $Qorden = 'desc_enc';

$a_dias_semana_breve = [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V', 6 => 'S', 7 => 'D'];
$a_nombre_mes_breve = [1 => 'Ene', 2 => 'feb', 3 => 'mar', 4 => 'abr', 5 => 'may', 6 => 'jun', 7 => 'jul', 8 => 'ago', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic'];


$columns_cuadricula = "[
    {'id': 'encargo', 'name' : 'Encargo', 'field' : 'encargo', 'width' : 250, 'cssClass' : 'cell-title', 'formatter': formato_encargos}";

echo '<TABLE>';
echo '<TR>';
echo '<TH class="cell-title" style:"width:250px">Encargo</TH>';

$titulo_sacd = [];
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
            $interval3 = new DateInterval(EncargoDia::INTERVAL_SEMANAL);
        }
        $a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
        foreach ($date_range as $date) {
            $num_dia = $date->format('Y-m-d');
            $dia_week = $date->format('N');
            $dia_week_sacd[$num_dia] = $date->format('N');
            $nom_dia = $a_dias_semana[$dia_week];

            $titulo_sacd[$num_dia] = $nom_dia;
            $columns_cuadricula .= ",
            {'id' : '" . $num_dia . "', 'name' : '" . $nom_dia . "', 'field' : '" . $num_dia . "', 'width' : 60, 'formatter': formato}";

            echo '<TH class=cell-title style:"width:60">' . $nom_dia . '</TH>';
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
            $interval3 = new DateInterval(EncargoDia::INTERVAL_DOMINGOS);
        }
        $a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
        foreach ($date_range as $date) {
            $num_dia = $date->format('Y-m-d');
            $dia_week = $date->format('N');
            $dia_week_sacd[$num_dia] = $date->format('N');
            $dia_mes = $date->format('d');
            if ($dia_mes < 7) {
                $nom_dia = $a_dias_semana[$dia_week];
            } else {
                $nom_dia = 'domingo ' . strval($dia_mes - 6);
                $dia_week_sacd[$num_dia] = 7;
            }

            $columns_cuadricula .= ",
            {'id' : '" . $num_dia . "', 'name' : '" . $nom_dia . "', 'field' : '" . $num_dia . "', 'width' : 60, 'formatter': formato}";

            echo '<TH class=cell-title style:"width:60">' . $nom_dia . '</TH>';

            $titulo_sacd[$num_dia] = $nom_dia;
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
            $interval3 = new DateInterval(EncargoDia::INTERVAL_MENSUAL);
        }
        $a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
        foreach ($date_range as $date) {
            $num_dia = $date->format('Y-m-d');
            $dia_week = $date->format('N');
            $dia_week_sacd[$num_dia] = $date->format('N');
            $dia_mes = $date->format('d');
            $nom_dia = $a_dias_semana[$dia_week] . ' ' . intdiv(date_diff($date, $oInicio)->format('%a'), 7) + 1;

            $columns_cuadricula .= ",
            {'id' : '" . $num_dia . "', 'name' : '" . $nom_dia . "', 'field' : '" . $num_dia . "', 'width' : 60, 'formatter': formato}";

            echo '<TH class=cell-title style:"width:60">' . $nom_dia . '</TH>';

            $titulo_sacd[$num_dia] = $nom_dia;
        }
        break;
    case EncargoDia::PLAN_DE_MISAS:
        $oInicio = new DateTimeLocal($Qempiezamin_rep);
        $oFin = new DateTimeLocal($Qempiezamax_rep);
        $interval = new DateInterval('P1D');
        $oFin->add($interval);
        $date_range = new DatePeriod($oInicio, $interval, $oFin);
        foreach ($date_range as $date) {
            $num_dia = $date->format('Y-m-d');
            $dia_week = $date->format('N');
            $dia_week_sacd[$num_dia] = $date->format('N');
            $dia_mes = $date->format('d');
            $num_mes = $date->format('m');
            $nom_dia = $a_dias_semana_breve[$dia_week] . ' ' . $dia_mes . '.' . $num_mes;
            $nom_dia2 = $a_dias_semana_breve[$dia_week] . '<br>' . $dia_mes . '.' . $num_mes;
            $columns_cuadricula .= ",
            {'id' : '" . $num_dia . "', 'name' : '" . $nom_dia . "', 'field' : '" . $num_dia . "', 'width' : 60, 'formatter': formato}";

            echo '<TH class=cell-title style:"width:60">' . $nom_dia2 . '</TH>';

            $titulo_sacd[$num_dia] = $nom_dia;
        }
        break;
}

$columns_cuadricula .= "]";

$data_cuadricula = [];

$EncargoTipoRepository = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);

$grupo = '8...';
//if (!empty($grupo)) {
$aWhere = [];
$aOperador = [];
$aWhere['id_tipo_enc'] = '^' . $grupo;
$aOperador['id_tipo_enc'] = '~';
$cEncargoTipos = $EncargoTipoRepository->getEncargoTipos($aWhere, $aOperador);

$posibles_encargo_tipo = [];
foreach ($cEncargoTipos as $oEncargoTipo) {
    if ($oEncargoTipo->getId_tipo_enc() >= 8100)
        $a_tipo_enc[] = $oEncargoTipo->getId_tipo_enc();
}

$EncargosZona = new EncargosZona($Qid_zona, $oInicio, $oFin, $Qorden);
$EncargosZona->setATipoEnc($a_tipo_enc);
$cEncargosZona = $EncargosZona->getEncargos();
foreach ($cEncargosZona as $oEncargo) {
    $id_enc = $oEncargo->getId_enc();
    $desc_enc = $oEncargo->getDesc_enc();
    $tipo_enc = $oEncargo->getId_tipo_enc();
//    echo $id_enc.$desc_enc.'<br>';
    $data_cols = [];
    $meta_dia = [];
    if (($QTipoPlantilla == EncargoDia::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantilla == EncargoDia::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantilla == EncargoDia::PLANTILLA_MENSUAL_TRES)) {
        $data_cols2 = [];
        $meta_dia2 = [];
        $data_cols3 = [];
        $meta_dia3 = [];
    }

    echo '</TR>';
    echo '<TR><TD>' . $desc_enc . '</TD>';

    foreach ($date_range as $date) {
//        $d++;
        $num_dia = $date->format('Y-m-d');
        $dia_week = $date->format('N');
        $dia_week_sacd[$num_dia] = $date->format('N');
        $dia_mes = $date->format('d');
        $num_mes = $date->format('m');

        switch (trim($QTipoPlantilla)) {
            case EncargoDia::PLANTILLA_SEMANAL_UNO:
            case EncargoDia::PLANTILLA_SEMANAL_TRES:
                $nom_dia = $a_dias_semana[$dia_week];
                break;
            case EncargoDia::PLANTILLA_DOMINGOS_UNO:
            case EncargoDia::PLANTILLA_DOMINGOS_TRES:
                if ($dia_mes < 7) {
                    $nom_dia = $a_dias_semana[$dia_week];
                } else {
                    $nom_dia = 'domingo ' . strval($dia_mes - 6);
                }
                break;
            case EncargoDia::PLANTILLA_MENSUAL_UNO:
            case EncargoDia::PLANTILLA_MENSUAL_TRES:
                $nom_dia = $a_dias_semana[$dia_week] . ' ' . intdiv(date_diff($date, $oInicio)->format('%a'), 7) + 1;
                break;
            case EncargoDia::PLAN_DE_MISAS:
                $nom_dia = $a_dias_semana_breve[$dia_week] . ' ' . $dia_mes . '.' . $num_mes;
                break;
        }


        $nom_dia = $date->format('D');
//echo $num_dia.'<br>';
        $data_cols["$num_dia"] = " -- ";
        $iniciales = ' -- ';

        $meta_dia["$num_dia"] = [
                "uuid_item" => "",
                "color" => "",
                "key" => '',
                "tstart" => '',
                "tend" => '',
                "observ" => '',
                "id_enc" => $id_enc,
                "dia" => $num_dia,
                "tipo" => 'misas',
        ];

        // sobreescribir los que tengo datos:
        $inicio_dia = $num_dia . ' 00:00:00';
        $fin_dia = $num_dia . ' 23:59:59';
        $aWhere = [
                'id_enc' => $id_enc,
                'tstart' => "'$inicio_dia', '$fin_dia'",
        ];
        $aOperador = [
                'tstart' => 'BETWEEN',
        ];
        $EncargoDiaRepository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
        $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);

        if (count($cEncargosDia) > 1) {
            $oEncargoDia = $cEncargosDia[0];
            $id_nom = $oEncargoDia->getId_nom();
            $oEncargoDia = $cEncargosDia[1];
            $id_nom = $oEncargoDia->getId_nom();
            exit(_("sólo debería haber uno") . '-' . $inicio_dia . '-' . $fin_dia . '-' . $id_enc);
        }

        $color = '';
        $texto = '';
        if (count($cEncargosDia) === 1) {
            $oEncargoDia = $cEncargosDia[0];
            $id_nom = $oEncargoDia->getId_nom();
            $estado = $oEncargoDia->getStatus();
            $hora_ini = $oEncargoDia->getTstart()->format('H:i');
            if ($hora_ini == '00:00')
                $hora_ini = '';
            $InicialesSacd = new InicialesSacd();
            $iniciales = $InicialesSacd->iniciales($id_nom);
            if (trim($QTipoPlantilla) == EncargoDia::PLAN_DE_MISAS) {
                if ($estado == EncargoDia::STATUS_PROPUESTA) {
                    $color = 'rojoclaro';
                }
                if ($estado == EncargoDia::STATUS_COMUNICADO_SACD) {
                    $color = 'amarilloclaro';
                }
                if ($estado == EncargoDia::STATUS_COMUNICADO_CTR) {
                    $color = 'verdeclaro';
                }
            }

            if (!isset($sacd_zona[$id_nom])) {
                $color = 'amarillo';
                $texto = 'No es de la zona';
            }

            $meta_dia["$num_dia"] = [
                    "uuid_item" => $oEncargoDia->getUuid_item()->value(),
                    "color" => $color,
                    "key" => "$id_nom#$iniciales",
                    "tstart" => $oEncargoDia->getTstart()->getHora(),
                    "tend" => $oEncargoDia->getTend()->getHora(),
                    "observ" => $oEncargoDia->getObserv(),
                    "id_enc" => $id_enc,
                    "dia" => $num_dia,
                    "tipo" => 'misas',
                    "texto" => $texto,
            ];
            // añadir '*' si tiene observaciones
            $iniciales .= " " . $hora_ini;
            $iniciales .= empty($oEncargoDia->getObserv()) ? '' : '*';
            $data_cols["$num_dia"] = $iniciales;
//            $data_cols[$num_dia] = $iniciales;
        }
        echo '<TD class=' . $color . '>' . $iniciales . '</TD>';

        if (($QTipoPlantilla == EncargoDia::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantilla == EncargoDia::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantilla == EncargoDia::PLANTILLA_MENSUAL_TRES)) {
            $data_cols2["$num_dia"] = " -- ";
            $data_cols3["$num_dia"] = " -- ";

            $date2 = new DateTime($num_dia);
            $date2->add($interval3);
            $num_dia2 = $date2->format('Y-m-d');
            $date3 = new DateTime($num_dia2);
            $date3->add($interval3);
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
                    "tipo" => 'misas',
                    "texto" => '',
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
                    "tipo" => 'misas',
                    "texto" => '',
            ];

            // sobreescribir los que tengo datos:
            $inicio_dia2 = $num_dia2 . ' 00:00:00';
            $fin_dia2 = $num_dia2 . ' 23:59:59';

            $inicio_dia3 = $num_dia3 . ' 00:00:00';
            $fin_dia3 = $num_dia3 . ' 23:59:59';
            $aWhere = [
                    'id_enc' => $id_enc,
                    'tstart' => "'$inicio_dia2', '$fin_dia2'",
            ];
            $aOperador = [
                    'tstart' => 'BETWEEN',
            ];
            $EncargoDiaRepository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
            $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);

            if (count($cEncargosDia) > 1) {
                exit(_("sólo debería haber uno"));
            }

            if (count($cEncargosDia) === 1) {
                $oEncargoDia = $cEncargosDia[0];
                $id_nom = $oEncargoDia->getId_nom();
                $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                if ($hora_ini === '00:00')
                    $hora_ini = '';
                $InicialesSacd = new InicialesSacd();
                $iniciales = $InicialesSacd->iniciales($id_nom);

                $color = '';
                $texto = '';
                if (!isset($sacd_zona[$id_nom])) {
                    $color = 'amarillo';
                    $texto = 'No es de la zona';
                }

                $meta_dia2["$num_dia"] = [
                        "uuid_item" => $oEncargoDia->getUuid_item()->value(),
                        "color" => $color,
                        "key" => "$id_nom#$iniciales",
                        "tstart" => $oEncargoDia->getTstart()->getHora(),
                        "tend" => $oEncargoDia->getTend()->getHora(),
                        "observ" => $oEncargoDia->getObserv(),
                        "id_enc" => $id_enc,
                        "dia" => $num_dia2,
                        "tipo" => 'misas',
                        "texto" => $texto,
                ];
                // añadir '*' si tiene observaciones
                $iniciales .= $hora_ini;
                $iniciales .= empty($oEncargoDia->getObserv()) ? '' : '*';
                $data_cols2["$num_dia"] = $iniciales;
            }

            $aWhere = [
                    'id_enc' => $id_enc,
                    'tstart' => "'$inicio_dia3', '$fin_dia3'",
            ];
            $aOperador = [
                    'tstart' => 'BETWEEN',
            ];
            $EncargoDiaRepository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
            $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);

            if (count($cEncargosDia) > 1) {
                exit(_("sólo debería haber uno"));
            }

            if (count($cEncargosDia) === 1) {
                $oEncargoDia = $cEncargosDia[0];
                $id_nom = $oEncargoDia->getId_nom();
                $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                if ($hora_ini == '00:00')
                    $hora_ini = '';
                $InicialesSacd = new InicialesSacd();
                $iniciales = $InicialesSacd->iniciales($id_nom);

                $color = '';
                $texto = '';
                if (!isset($sacd_zona[$id_nom])) {
                    $color = 'amarillo';
                    $texto = 'No es de la zona';
                }

                $meta_dia3["$num_dia"] = [
                        "uuid_item" => $oEncargoDia->getUuid_item()->value(),
                        "color" => $color,
                        "key" => "$id_nom#$iniciales",
                        "tstart" => $oEncargoDia->getTstart()->getHora(),
                        "tend" => $oEncargoDia->getTend()->getHora(),
                        "observ" => $oEncargoDia->getObserv(),
                        "id_enc" => $id_enc,
                        "dia" => $num_dia3,
                        "tipo" => 'misas',
                        "texto" => $texto,
                ];
                // añadir '*' si tiene observaciones
                $iniciales .= " " . $hora_ini;
                $iniciales .= empty($oEncargoDia->getObserv()) ? '' : '*';
                $data_cols3["$num_dia"] = $iniciales;
            }
        }
    }

    $data_cols["encargo"] = $desc_enc;
    $data_cols["id_nom"] = '';
    if (substr($tipo_enc, 0, 2) == 81) {
        $data_cols["color_encargo"] = 'azulclaro';
    }
    if (substr($tipo_enc, 0, 2) == 82) {
        $data_cols["color_encargo"] = 'violetaclaro';
    }
    if (substr($tipo_enc, 0, 2) == 83) {
        $data_cols["color_encargo"] = 'amarilloclaro';
    }
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
echo '</TR>';
//echo '</TABLE>';

$contador_1a_sacd = [];
$contador_total_sacd = [];
$esta_sacd = [];
$donde_esta_sacd = [];
$ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
foreach ($sacd_zona as $id_nom => $nombre_sacd) {
//    echo $id_nom.'->'.$nombre_sacd.'<br>';
    $contador_sacd[$id_nom] = [];
    $contador_sacd[$id_nom]['nombre'] = $nombre_sacd;
    foreach ($date_range as $date) {
        $num_dia = $date->format('Y-m-d');
        $contador_1a_sacd[$id_nom][$num_dia] = 0;
        $contador_total_sacd[$id_nom][$num_dia] = 0;
        $esta_sacd[$id_nom][$num_dia] = 1;
    }

    $aWhereAct = [];
    $aOperadorAct = [];
    $sInicio_iso = $oInicio->getIso();
    $sFin_iso = $oFin->getIso();
    $aWhereAct['f_ini'] = $sFin_iso;
    $aOperadorAct['f_ini'] = '<=';
    $aWhereAct['f_fin'] = $sInicio_iso;
    $aOperadorAct['f_fin'] = '>=';
    $aWhereAct['status'] = StatusId::ACTUAL;
    $aWhere = ['id_nom' => $id_nom];
    $aOperador = [];

    $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
    $cAsistentes = $ActividadCargoRepository->getAsistenteCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);

    foreach ($cAsistentes as $aAsistente) {
        $id_activ = $aAsistente['id_activ'];
        $propio = $aAsistente['propio'];
        $id_cargo = empty($aAsistente['id_cargo']) ? '' : $aAsistente['id_cargo'];

        // Seleccionar sólo las del periodo
        $aWhereAct['id_activ'] = $id_activ;
        $cActividades = $ActividadRepository->getActividades($aWhereAct, $aOperadorAct);
        if (is_array($cActividades) && count($cActividades) == 0) continue;

        $oActividad = $cActividades[0]; // sólo debería haber una.
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $dInicioActividad = $oActividad->getF_ini();
        $sInicioActividad = $dInicioActividad->format('Y-m-d');
        $dFinActividad = $oActividad->getF_fin();
        $sFinActividad = $dFinActividad->format('Y-m-d');
        $nom_activ = $oActividad->getNom_activ();
        $oTipoActividad = new TiposActividades($id_tipo_activ);
        $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
        $nom_llarg = $nom_activ;

//        echo $nom_llarg.'<br>';
//        echo $id_nom.'<br>';
        if (isset($esta_sacd[$id_nom][$sInicioActividad])) {
            if ($esta_sacd[$id_nom][$sInicioActividad] == 1) {
                $esta_sacd[$id_nom][$sInicioActividad] = 2;
            }
        }
        $esta_sacd[$id_nom][$sFinActividad] = -1;
        $donde_esta_sacd[$id_nom][$sFinActividad] = $nom_llarg;
        $dInicioActividadmas1 = date_add($dInicioActividad, $interval);
        $date_range_actividad = new DatePeriod($dInicioActividadmas1, $interval, $dFinActividad);
        foreach ($date_range_actividad as $date) {
            $num_dia = $date->format('Y-m-d');
//        echo $num_dia.'<br>';
            $esta_sacd[$id_nom][$num_dia] = 0;
            $donde_esta_sacd[$id_nom][$num_dia] = $nom_llarg;
        }
    }
    // ++++++++++++++ Añado las ausencias +++++++++++++++
    $aWhereE = [];
    $aOperadorE = [];
    $aWhereE['id_nom'] = $id_nom;
    $aWhereE['f_ini'] = "'$sFin_iso'";
    $aOperadorE['f_ini'] = '<=';
    $aWhereE['f_fin'] = "'$sInicio_iso'";
    $aOperadorE['f_fin'] = '>=';
    $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
    $EncargoSacdHorarioRepository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);
    $cAusencias = $EncargoSacdHorarioRepository->getEncargoSacdHorarios($aWhereE, $aOperadorE);
    foreach ($cAusencias as $oTareaHorarioSacd) {
        $id_enc = $oTareaHorarioSacd->getId_enc();
        $oF_ini = $oTareaHorarioSacd->getF_ini();
        $oF_fin = $oTareaHorarioSacd->getF_fin();

        $oEncargo = $EncargoRepository->findById($id_enc);
        $id_tipo_enc = $oEncargo->getId_tipo_enc();
        $id = (string)$id_tipo_enc;
        if ($id[0] != 7 && $id[0] != 4) {
            continue;
        }

        //para el caso de que la actividad comience antes
        //del periodo de inicio obligo a que tome una hora de inicio
//                if ($oIniPlanning > $oF_ini) {
//                    $ini = $inicio_local;
//                    $hini = '5:00';
//                } else {
        $ini = (string)$oF_ini->getFromLocal();
//                    $hini = empty($h_ini) ? '5:00' : (string)$h_ini;
//                }
        $fi = (string)$oF_fin->getFromLocal();
//                $hfi = empty($h_fin) ? '22:00' : (string)$h_fin;

//                $propio = "p";
        $nom_llarg = $oEncargo->getDesc_enc();
        $nom_curt = ($nom_llarg[0] === 'A') ? 'a' : 'x';
        if ($ini != $fi) {
            $nom_llarg .= " ($ini-$fi)";
        } else {
            $nom_llarg .= " ($ini)";
        }

//                echo $nom_llarg;
        if (isset($esta_sacd[$id_nom][$ini])) {
            if ($esta_sacd[$id_nom][$ini] == 1) {
                $esta_sacd[$id_nom][$ini] = 2;
            }
        }
        $esta_sacd[$id_nom][$fi] = -1;
        $donde_esta_sacd[$id_nom][$fi] = $nom_llarg;
        $oF_finmas1 = date_add($oF_fin, $interval);
        $date_range_actividad = new DatePeriod($oF_ini, $interval, $oF_finmas1);
        foreach ($date_range_actividad as $date) {
            $num_dia = $date->format('Y-m-d');
            //        echo $num_dia.'<br>';
            $esta_sacd[$id_nom][$num_dia] = 0;
            $donde_esta_sacd[$id_nom][$num_dia] = $nom_llarg;
        }
    }
}

//echo '<TABLE>';
echo '<TR>';
echo '<TH class="cell-title" style:"width:250px">Sacerdote</TH>';

$data_cols = [];
$data_cols['encargo'] = 'Sacerdotes';
$data_cols['id_nom'] = '';
$data_cols["color_encargo"] = 'titulo';
$meta = [];
foreach ($date_range as $date) {
    $num_dia = $date->format('Y-m-d');
    $dia_week = $date->format('N');
    $dia_mes = $date->format('d');
    $num_mes = $date->format('m');

    $data_cols["$num_dia"] = $titulo_sacd[$num_dia];
    $meta["$num_dia"] = [
            "texto" => $num_dia,
            "tipo" => 'titulo',
    ];
    echo '<TH class=cell-title style:"width:60">' . $titulo_sacd[$num_dia] . '</TH>';
}
echo '</TR>';
$data_cols["meta"] = $meta;
$data_cuadricula[] = $data_cols;

$aWhere = [];
$aWhere['id_zona'] = $Qid_zona;
$aOperador = [];
$ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
$cZonaSacd = $ZonaSacdRepository->getZonasSacds($aWhere, $aOperador);
foreach ($cZonaSacd as $oZonaSacd) {
    $data_cols = [];
    $id_nom = $oZonaSacd->getId_nom();
    $InicialesSacd = new InicialesSacd();
    $nombre_sacd = $InicialesSacd->nombre_sacd($id_nom);
    $iniciales = $InicialesSacd->iniciales($id_nom);
    $key = $iniciales . '#' . $id_nom;
    $lista_sacd[$key] = $nombre_sacd;
    $esta_en_zona[$key] = array('', $oZonaSacd->getDw1(), $oZonaSacd->getDw2(), $oZonaSacd->getDw3(), $oZonaSacd->getDw4(), $oZonaSacd->getDw5(), $oZonaSacd->getDw6(), $oZonaSacd->getDw7());
}

ksort($lista_sacd);
$EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
foreach ($lista_sacd as $key => $nombre_sacd) {
    $exp_key = explode('#', $key);
    $id_nom = $exp_key[1];
    $data_cols['encargo'] = $nombre_sacd;
    echo '<TR><TD>' . $nombre_sacd . '</TD>';
    $data_cols['id_nom'] = $id_nom;
//    echo $nombre_sacd.$id_nom.'<br>';
    foreach ($date_range as $date) {
        $inicio_dia = $date->format('Y-m-d') . ' 00:00:00';
        $fin_dia = $date->format('Y-m-d') . ' 23:59:59';
//echo $inicio_dia.'-'.$fin_dia.'<br>';
//echo 'id nom: '.$id_nom.'<br>';
        $texto = '';
        $color_fondo = '';

        $aWhere = [
                'id_nom' => $id_nom,
                'tstart' => "'$inicio_dia', '$fin_dia'",
        ];
        $aWhere['_ordre'] = 'tstart';
        $aOperador = [
                'tstart' => 'BETWEEN',
        ];
        $EncargoDiaRepository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
        $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);

        $misas_dia = 0;
        $misas_1a_hora = 0;
        $misas_dia_zona = 0;
        $misas_1a_hora_zona = 0;
        foreach ($cEncargosDia as $oEncargoDia) {
            $id_enc = $oEncargoDia->getId_enc();
//            echo 'id_enc: '.$id_enc.'<br>';
            $oEncargo = $EncargoRepository->findById($id_enc);
            $id_tipo_enc = $oEncargo->getId_tipo_enc();
            $id_zona_enc = $oEncargo->getId_zona();
//            echo 'tipo: '.$id_tipo_enc.' zona: '.$id_zona_enc.'<br>';
            if (substr($id_tipo_enc, 1, 1) == '1') {
                $misas_dia++;
                $misas_1a_hora++;
                if ($Qid_zona == $id_zona_enc) {
                    $misas_dia_zona++;
                    $misas_1a_hora_zona++;
                }
            }
            if (substr($id_tipo_enc, 1, 1) == '2') {
                $misas_dia++;
                if ($Qid_zona == $id_zona_enc) {
                    $misas_dia_zona++;
                }
            }
            //           echo $misas_dia.$misas_1a_hora.'<br>';
        }
        //       echo $misas_dia.$misas_1a_hora.'<br>';
        $num_dia = $date->format('Y-m-d');
        $dws = $dia_week_sacd[$num_dia];
//echo $num_dia.'-'.$dws.'='.$esta_en_zona[$dws].'<br>';
        $color_fondo = 'verdeclaro';
        $texto = '';
        if ($misas_dia > 2) {
            $texto = 'Este día tiene más de dos Misas';
            $color_fondo = 'rojo';
        }
        if ($misas_dia == 2) {
            $texto = 'Este día tiene dos Misas';
            $color_fondo = 'amarillo';
        }
        if (($misas_dia == 0) && ($esta_en_zona[$key][$dws])) {
            $texto = 'Este día no tiene ninguna Misa';
            $color_fondo = 'verde';
        }
        if (($misas_dia == 0) && (!$esta_en_zona[$key][$dws])) {
            $texto = 'Este día no tiene ninguna Misa';
            $color_fondo = 'azulclaro';
        }
        if ($misas_1a_hora == 2) {
            $texto = 'Tiene dos Misas a primera hora';
            $color_fondo = 'rojo';
        }


        if ($esta_en_zona[$key][$dws]) {
            $data_cols[$num_dia] = 'SI';
        } else {
            if ($misas_1a_hora_zona > 0) {
                $color_fondo = 'rojo';
                $texto = 'No está en la zona y tiene Misa a primera hora';
            }
            $data_cols[$num_dia] = 'NO';
        }
        if ($esta_sacd[$id_nom][$num_dia] < 1) {
            if ($misas_1a_hora_zona > 0) {
//                echo '1a: '.$misas_1a_hora.'<br>';
                $color_fondo = 'rojo';
            }
            $texto = 'Está en ' . $donde_esta_sacd[$id_nom][$num_dia];
            $data_cols[$num_dia] = '--';
        }
//        echo $id_nom.'-'.$color_fondo.'-'.$texto.'<br>';
        $meta_sacd["$num_dia"] = [
                "color" => $color_fondo,
                "texto" => $texto,
                "tipo" => 'sacd',
        ];

//        echo $num_dia.$meta_sacd["$num_dia"]['tipo'].$meta_sacd["$num_dia"]['color'].$meta_sacd["$num_dia"]['texto'].'<br>';
        echo '<TD>' . $data_cols["$num_dia"] . '</TD>';
    }
    $data_cols["meta"] = $meta_sacd;
    $data_cuadricula[] = $data_cols;
    echo '</TR>';
}

echo '</TABLE>';

$json_data_cuadricula = json_encode($data_cuadricula);

$url_cuadricula_update = 'apps/misas/controller/cuadricula_update.php';
$oHash_cuadricula_update = new Hash();
$oHash_cuadricula_update->setUrl($url_cuadricula_update);
$oHash_cuadricula_update->setCamposForm('dia!id_enc!key!observ!tend!tstart!uuid_item!tipo_plantilla!id_zona');
$h_cuadricula_update = $oHash_cuadricula_update->linkSinVal();

$url_desplegable_sacd = 'apps/misas/controller/desplegable_sacd.php';
$oHash_desplegable_sacd = new Hash();
$oHash_desplegable_sacd->setUrl($url_desplegable_sacd);
$oHash_desplegable_sacd->setCamposForm('id_zona!id_sacd!seleccion!dia');
$h_desplegable_sacd = $oHash_desplegable_sacd->linkSinVal();

$url_ver_cuadricula_zona = 'apps/misas/controller/ver_cuadricula_zona.php';
$oHash_ver_cuadricula_zona = new Hash();
$oHash_ver_cuadricula_zona->setUrl($url_ver_cuadricula_zona);
$oHash_ver_cuadricula_zona->setCamposForm('id_zona!tipo_plantilla!orden!seleccion!periodo!empiezamin!empiezamax!fila!columna');
$h_ver_cuadricula_zona = $oHash_ver_cuadricula_zona->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
        'columns_cuadricula' => $columns_cuadricula,
        'json_data_cuadricula' => $json_data_cuadricula,
        'url_desplegable_sacd' => $url_desplegable_sacd,
        'h_desplegable_sacd' => $h_desplegable_sacd,
        'url_ver_cuadricula_zona' => $url_ver_cuadricula_zona,
        'h_ver_cuadricula_zona' => $h_ver_cuadricula_zona,
        'id_zona' => $Qid_zona,
        'tipo_plantilla' => $QTipoPlantilla,
        'orden' => $Qorden,
        'seleccion' => $Qseleccion,
        'periodo' => $Qperiodo,
        'empieza_min' => $Qempiezamin,
        'empieza_max' => $Qempiezamax,
        'fila' => $Qfila,
        'columna' => $Qcolumna,
        'h_cuadricula_update' => $h_cuadricula_update,
];

//$oView = new ViewTwig('misas/controller');
//echo $oView->render('ver_cuadricula_zona.html.twig', $a_campos);