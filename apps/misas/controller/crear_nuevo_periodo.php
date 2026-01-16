<?php


// INICIO Cabecera global de URL de controlador *********************************
use core\ViewTwig;
use Ramsey\Uuid\Uuid as RamseyUuid;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\EncargoConstants;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\EncargosZona;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\PlantillaConfig;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\value_objects\EncargoDiaId;
use src\misas\domain\value_objects\EncargoDiaTend;
use src\misas\domain\value_objects\EncargoDiaTstart;
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
$QTipoPlantilla = (string)filter_input(INPUT_POST, 'tipoplantilla');
$Qseleccion = (string)filter_input(INPUT_POST, 'seleccion');

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$un_dia = new DateInterval('P1D');

//echo 'zona:'.$Qid_zona.' tipoplantilla: '.$QTipoPlantilla.' periodo '.$Qperiodo.'<br>';

switch ($Qperiodo) {
    case "proxima_semana":
        $dia_week = date('N');
//        echo 'dia:'.$dia_week.'<br>';
        $empiezamin = new DateTimeLocal(date('Y-m-d'));
        $intervalo = 'P' . (8 - $dia_week) . 'D';
        $empiezamin->add(new DateInterval($intervalo));
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
//        echo 'empieza'.$Qempiezamin_rep.'<br>';
        $intervalo = 'P7D';
        $empiezamax = $empiezamin;
        $empiezamax->add(new DateInterval($intervalo));
        $empiezamax->sub($un_dia);
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
//        echo 'fin'.$Qempiezamax_rep.'<br>';
        break;
    case "proximo_mes":
        $proximo_mes = (int)date('m') + 1;
        $anyo = (int)date('Y');
        if ($proximo_mes === 13) {
            $proximo_mes = 1;
            $anyo++;
        }
        $empiezamin = new DateTimeLocal(date($anyo . '-' . $proximo_mes . '-01'));
        $Qempiezamin_rep = $empiezamin->format('Y-m-d');
//        echo 'empieza'.$Qempiezamin_rep.'<br>';
        $siguiente_mes = $proximo_mes + 1;
        if ($siguiente_mes === 13) {
            $siguiente_mes = 1;
            $anyo++;
        }
        $empiezamax = new DateTimeLocal(date($anyo . '-' . $siguiente_mes . '-01'));
        $empiezamax->sub($un_dia);
        $Qempiezamax_rep = $empiezamax->format('Y-m-d');
//        echo 'fin'.$Qempiezamax_rep.'<br>';
        break;
    default:
        $partes_min = explode('/', $Qempiezamin);
        $Qempiezamin_rep = $partes_min[2] . '-' . $partes_min[1] . '-' . $partes_min[0];
        $partes_max = explode('/', $Qempiezamax);
        $Qempiezamax_rep = $partes_max[2] . '-' . $partes_max[1] . '-' . $partes_max[0];
}

$sInicio = $Qempiezamin_rep . ' 00:00:00';
$sFin = $Qempiezamax_rep . ' 23:59:59';

$a_dias_semana_breve = [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V', 6 => 'S', 7 => 'D'];
$a_nombre_mes_breve = [1 => 'Ene', 2 => 'feb', 3 => 'mar', 4 => 'abr', 5 => 'may', 6 => 'jun', 7 => 'jul', 8 => 'ago', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic'];

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
    $nom_dia = $a_dias_semana_breve[$dia_week] . ' ' . $dia_mes;
    $columns_cuadricula[] =
        ["id" => "$num_dia", "name" => "$nom_dia", "field" => "$num_dia", "width" => 80, "cssClass" => "cell-title"];
    $columns_cuadricula[] =
        ["id" => "$num_dia", "name" => "$nom_dia", "field" => "$num_dia", "width" => 80, "cssClass" => "cell-title"];
}

//        $data_sacd = [];
$aWhere = [];
$aWhere['id_zona'] = $Qid_zona;
$aOperador = [];
$ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
$cZonaSacd = $ZonaSacdRepository->getZonasSacds($aWhere, $aOperador);
$contador_1a_sacd = [];
$contador_total_sacd = [];
$esta_sacd = [];
$donde_esta_sacd = [];
$ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
$InicialesSacdService = $GLOBALS['container']->get(InicialesSacdService::class);
foreach ($cZonaSacd as $oZonaSacd) {
    $id_nom = $oZonaSacd->getId_nom();
    $contador_1a_sacd[$id_nom] = [];
    $contador_total_sacd[$id_nom] = [];
    $nombre_sacd = $InicialesSacdService->obtenerNombreConIniciales($id_nom);
    // echo $id_nom . '->' . $nombre_sacd . '<br>';
    $contador_sacd[$id_nom]['nombre'] = $nombre_sacd;
    foreach ($date_range as $date) {
        $num_dia = $date->format('Y-m-d');
        $contador_1a_sacd[$id_nom][$num_dia] = 0;
        $contador_total_sacd[$id_nom][$num_dia] = 0;
        $esta_sacd[$id_nom][$num_dia] = 1;
    }

    $aWhereAct = [];
    $aOperadorAct = [];
    $aWhereAct['f_ini'] = "'$sFin'";
    $aOperadorAct['f_ini'] = '<=';
    $aWhereAct['f_fin'] = "'$sInicio'";
    $aOperadorAct['f_fin'] = '>=';
    $aWhereAct['status'] = StatusId::ACTUAL;
    $aWhere = ['id_nom' => $id_nom];
    $aOperador = [];
//    echo 'inicio: ' . $sInicio . ' fin: ' . $sFin . '<br>';
    $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
    $cAsistentes = $ActividadCargoRepository->getAsistenteCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);
    foreach ($cAsistentes as $aAsistente) {
        $id_activ = $aAsistente['id_activ'];
        $propio = $aAsistente['propio'];
        $id_cargo = empty($aAsistente['id_cargo']) ? '' : $aAsistente['id_cargo'];

        // Seleccionar sólo las del periodo
        $aWhereAct['id_activ'] = $id_activ;
        $cActividades = $ActividadRepository->getActividades($aWhereAct, $aOperadorAct);
        if (is_array($cActividades) && count($cActividades) === 0) continue;

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
        $oTipoActividad = new TiposActividades($id_tipo_activ);
        $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
        $nom_llarg = $nom_activ;

        if (isset($esta_sacd[$id_nom][$sInicioActividad])) {
            if ($esta_sacd[$id_nom][$sInicioActividad] === 1) {
                $esta_sacd[$id_nom][$sInicioActividad] = 2;
            }
        }
        $esta_sacd[$id_nom][$sFinActividad] = -1;
        $dInicioActividadmas1 = date_add($dInicioActividad, $interval);
        $date_range_actividad = new DatePeriod($dInicioActividadmas1, $interval, $dFinActividad);
        foreach ($date_range_actividad as $date) {
            $num_dia = $date->format('Y-m-d');
            $esta_sacd[$id_nom][$num_dia] = 0;
            $donde_esta_sacd[$id_nom][$num_dia] = $nom_llarg;
//            echo $id_nom . ' ' . $num_dia . ' està a ' . $nom_llarg . '<br>';
        }

    }

    // ++++++++++++++ Añado las ausencias +++++++++++++++
    $aWhereE = [];
    $aOperadorE = [];
    $aWhereE['id_nom'] = $id_nom;
    $sInicio_iso = $oInicio->getIso();
    $sFin_iso = $oFin->getIso();
    $aWhereE['f_ini'] = "'$sInicio_iso'";
    $aOperadorE['f_ini'] = '<=';
    $aWhereE['f_fin'] = "'$sFin_iso'";
    $aOperadorE['f_fin'] = '>=';
//                $aWhereE['f_ini'] = "'$sFin_iso'";
//                $aOperadorE['f_ini'] = '<=';
//                $aWhereE['f_fin'] = "'$sInicio_iso'";
//                $aOperadorE['f_fin'] = '>=';
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

        $ini = (string)$oF_ini->getFromLocal();
        $fi = (string)$oF_fin->getFromLocal();

        $nom_llarg = $oEncargo->getDesc_enc();
        $nom_curt = ($nom_llarg[0] === 'A') ? 'a' : 'x';
        if ($ini != $fi) {
            $nom_llarg .= " ($ini-$fi)";
        } else {
            $nom_llarg .= " ($ini)";
        }

//        echo 'ausencia: ' . $id_nom . ' ' . $nom_llarg;
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
//            echo $id_nom . ' ' . $num_dia . ' està a ' . $nom_llarg . '<br>';
        }
    }
}

$EncargoTipoRepository = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);

$grupo = '8...';
$aWhere = [];
$aOperador = [];
$aWhere['id_tipo_enc'] = '^' . $grupo;
$aOperador['id_tipo_enc'] = '~';
$cEncargoTipos = $EncargoTipoRepository->getEncargoTipos($aWhere, $aOperador);

$a_tipo_enc = [];
$posibles_encargo_tipo = [];
foreach ($cEncargoTipos as $oEncargoTipo) {
    if ($oEncargoTipo->getId_tipo_enc() >= 8100) {
        $a_tipo_enc[] = $oEncargoTipo->getId_tipo_enc();
    }
}

$data_cuadricula = [];
$orden = 'prioridad';

$hay_bendicion = [];
// Miro qué días hay bendición
foreach ($date_range as $date) {
    $bendicion = 'NO';

    $dia_completo = $date->format('Y-m-d');
    $dia_semana = date('w', strtotime($dia_completo));

    $temps = '';

    $partes = explode('-', $dia_completo);
    $dia = intval($partes[2]);
    $mes = intval($partes[1]);
    $anyo = intval($partes[0]);
//    $date_dia_completo= new DateTime($dia_completo);
//    echo 'd-m-Y' . $dia . '.' . $mes . '.' . $anyo . '<br>';
    //DPascua

    $DPascua = new DateTime("$anyo-03-21");
    $dias_Pascua = easter_days($anyo);
    $DPascua->add(new DateInterval("P{$dias_Pascua}D"));
    $sDPascua = $DPascua->format('Y-m-d');
    $tiempo = strtotime($dia_completo) - strtotime($sDPascua);
    $hores = $tiempo / 3600;
    $dies = $hores / 24;
    $semana = intdiv($dies, 7);
    if (($dies >= -46) && ($dies < 0))
        $temps = 'Q';
    if (($dies >= 0) && ($dies < 50))
        $temps = 'P';
    // Madre de Dios, Reyes, cumpleaños nP
    if (($mes == 1) && (($dia == 1) || ($dia == 6) || ($dia == 9)))
        $bendicion = 'SI';
    // Fundación sección de mujeres
    if (($mes == 2) && ($dia == 14))
        $bendicion = 'SI';
    // S. José
    if ((($mes == 3) && ($dia == 19)) || (($mes == 4) && ($dia >= 1) && ($dia <= 5) && ($temps === 'P') && ($semana == 2) && ($dia_semana == 2)) || (($temps === 'Q') && ($dia_semana == 1) && ($dia == 20) && ($mes == 3)))
        $bendicion = 'SI';
    // Anunciación
    if ((($mes == 3) && ($dia == 25)) || (((($mes == 4) && ($dia >= 1) && ($dia <= 9)) || (($mes == 3) && ($dia == 31))) && ($temps === 'P') && ($semana == 2) && ($dia_semana == 1)) || (($temps === 'Q') && ($dia_semana == 1) && ($dia == 26) && ($mes == 3)))
        // aniversario ordenación nP
        if (($mes == 3) && ($dia == 28))
            $bendicion = 'SI';
    // aniversario 1a Comunión nP
    if (($mes == 4) && ($dia == 23))
        $bendicion = 'SI';
    // Beato Álvaro
    if (($mes == 5) && ($dia == 12))
        $bendicion = 'SI';
    // S. Juan, S. Josemaría, S. Pedro
    if (($mes == 6) && (($dia == 24) || ($dia == 26) || ($dia == 29)))
        $bendicion = 'SI';
    // Asunción
    if (($mes == 8) && ($dia == 15))
        $bendicion = 'SI';
    // Santa Cruz, S. Arcángeles
    if (($mes == 9) && (($dia == 14) || ($dia == 29)))
        $bendicion = 'SI';
    // Fundación Opus Dei, Canonización nP
    if (($mes == 10) && (($dia == 2) || ($dia == 6)))
        $bendicion = 'SI';
    // Todos los santos, aniversario Prelatura
    if (($mes == 11) && (($dia == 1) || ($dia == 28)))
        $bendicion = 'SI';
    // Inmaculada, S. Juan
    if (($mes == 12) && (($dia == 8) || ($dia == 27)))
        $bendicion = 'SI';

    //Cristo Rey
    if (($mes == 11) && ($dia >= 19) && ($dia <= 25) && ($dia_semana == 0))
        $bendicion = 'SI';

    //Sagrada Familia
    if (($mes == 12) && ($dia > 25) && ($dia_semana == 0))
        $bendicion = 'SI';
    if (($mes == 12) && ($dia == 30) && ($dia_semana == 5))
        $bendicion = 'SI';

    //Bautismo del Señor
    if (($mes == 1) && ($dia > 6) && ($dia < 14) && ($dia_semana == 7))
        $bendicion = 'SI';
    //Ascensión
    $Ascension = new DateTime("$anyo-03-21");
    $DP = easter_days($anyo);
    $dias_Ascension = $DP + 42;
    $Ascension->add(new DateInterval("P{$dias_Ascension}D"));
    if (($mes == $Ascension->format('n')) && ($dia == $Ascension->format('j')))
        $bendicion = 'SI';
    //Pentecostés
    $pentecostes = new DateTime("$anyo-03-21");
    $dias_pentecostes = $DP + 49;
    $pentecostes->add(new DateInterval("P{$dias_pentecostes}D"));
    if (($mes == $pentecostes->format('n')) && ($dia == $pentecostes->format('j')))
        $bendicion = 'SI';
    //Santísima Trinidad
    $ST = new DateTime("$anyo-03-21");
    $dias_ST = $DP + 56;
    $ST->add(new DateInterval("P{$dias_ST}D"));
    if (($mes == $ST->format('n')) && ($dia == $ST->format('j')))
        $bendicion = 'SI';
    //Corpus
    $Corpus = new DateTime("$anyo-03-21");
    $dias_Corpus = $DP + 63;
    $Corpus->add(new DateInterval("P{$dias_Corpus}D"));
    if (($mes == $Corpus->format('n')) && ($dia == $Corpus->format('j')))
        $bendicion = 'SI';
    //Sagra Cor
    $SC = new DateTime("$anyo-03-21");
    $dias_SC = $DP + 68;
    $SC->add(new DateInterval("P{$dias_SC}D"));
    if (($mes == $SC->format('n')) && ($dia == $SC->format('j')))
        $bendicion = 'SI';

    // aniversario elección del Papa
    if (($mes == 3) && ($dia == 13))
        $bendicion = 'SI';
    // aniversario elección del Padre
    if (($mes == 1) && ($dia == 23))
        $bendicion = 'SI';
    // cumpleaños del Padre
    if (($mes == 10) && ($dia == 27))
        $bendicion = 'SI';
    // Santo del Padre
    if (($mes == 5) && ($dia == 30))
        $bendicion = 'SI';

    if ($dia_semana == 6)
        $bendicion = 'SI';


    //Comienzo una semana antes para que sea Domingo de Ramos y no Domingo de Pascua (así el desplazamiento es siempre positivo)
    $DiasSantos = new DateTime("$anyo-03-14");
    $DP = easter_days($anyo);
    $DiasSantos->add(new DateInterval("P{$DP}D"));
    //En Semana Santa no hay bendiciones
    for ($i = 1; $i <= 7; $i++) {
        if (($mes == $DiasSantos->format('n')) && ($dia == $DiasSantos->format('j'))) {
            $bendicion = 'NO';
        }
        $DiasSantos->add(new DateInterval("P1D"));
    }
    $hay_bendicion[$dia_completo] = $bendicion;
}

$EncargosZona = new EncargosZona($Qid_zona, $oInicio, $oFin, $orden);
$EncargosZona->setATipoEnc($a_tipo_enc);
$cEncargosZona = $EncargosZona->getEncargos();
foreach ($cEncargosZona as $oEncargo) {
    $id_enc = $oEncargo->getId_enc();
    $id_tipo = $oEncargo->getId_tipo_enc();
    $desc_enc = $oEncargo->getDesc_enc();

    $aWhere = [
        'id_enc' => $id_enc,
        'tstart' => "'$sInicio', '$sFin'",
    ];
    $aOperador = [
        'tstart' => 'BETWEEN',
    ];

    //Borro los encargos de la zona ya asignados en ese periodo
    $EncargoDiaRepository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
    $cEncargosaBorrar = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);
    foreach ($cEncargosaBorrar as $oEncargoaBorrar) {
        $EncargoDiaRepository->Eliminar($oEncargoaBorrar);
    }

//    echo $desc_enc . '-' . $id_tipo . '<br>';
//Si el encargo es una bendición miro si hay bendición ese día. 


    $data_cols = [];
    $meta_dia = [];
    foreach ($date_range as $date) {
        $ok_encargo = false;
        $dia_completo = $date->format('Y-m-d');

        if (($id_tipo != 8300) || ($hay_bendicion[$dia_completo] === 'SI')) {
            $num_dia = $date->format('Y-m-d');
            $nom_dia = $date->format('D');
            if (($QTipoPlantilla == PlantillaConfig::PLANTILLA_SEMANAL_UNO) || ($QTipoPlantilla == PlantillaConfig::PLANTILLA_SEMANAL_TRES)) {
                $dia_week = $date->format('N');
                $dia_plantilla = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_UNO);
                $intervalo_plantilla = 'P' . ($dia_week - 1) . 'D';
                $dia_plantilla->add(new DateInterval($intervalo_plantilla));
//                echo 'DIA PLANTILLA: ' . $dia_plantilla->format('d-m-Y') . '<br>';
            }

            if ($QTipoPlantilla == PlantillaConfig::PLANTILLA_SEMANAL_TRES) {
                $dia_week = $date->format('N');
                $dia_plantilla2 = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_DOS);
                $intervalo_plantilla = 'P' . ($dia_week - 1) . 'D';
                $dia_plantilla2->add(new DateInterval($intervalo_plantilla));

                $dia_plantilla3 = new DateTimeLocal(PlantillaConfig::INICIO_SEMANAL_TRES);
                $intervalo_plantilla = 'P' . ($dia_week - 1) . 'D';
                $dia_plantilla3->add(new DateInterval($intervalo_plantilla));
            }

            if (($QTipoPlantilla == PlantillaConfig::PLANTILLA_DOMINGOS_UNO) || ($QTipoPlantilla == PlantillaConfig::PLANTILLA_DOMINGOS_TRES)) {
                $dia_week = $date->format('N');
                $dia_plantilla = new DateTimeLocal(PlantillaConfig::INICIO_DOMINGOS_UNO);

                if ($dia_week == 7) {
                    $num_mes = $date->format('d');
                    $num_semana = intdiv(($num_mes - 1), 7);
                    $intervalo_plantilla = 'P' . ($dia_week + $num_semana - 1) . 'D';
                } else {
                    $intervalo_plantilla = 'P' . ($dia_week - 1) . 'D';
                }
                $dia_plantilla->add(new DateInterval($intervalo_plantilla));
            }

            if ($QTipoPlantilla == PlantillaConfig::PLANTILLA_DOMINGOS_TRES) {
                $dia_week = $date->format('N');
                $dia_plantilla2 = new DateTimeLocal(PlantillaConfig::INICIO_DOMINGOS_DOS);

                if ($dia_week == 7) {
                    $num_mes = $date->format('d');
                    $num_semana = intdiv(($num_mes - 1), 7);
                    $intervalo_plantilla = 'P' . ($dia_week + $num_semana - 1) . 'D';
                } else {
                    $intervalo_plantilla = 'P' . ($dia_week - 1) . 'D';
                }
                $dia_plantilla2->add(new DateInterval($intervalo_plantilla));

                $dia_plantilla3 = new DateTimeLocal(PlantillaConfig::INICIO_DOMINGOS_TRES);

                if ($dia_week == 7) {
                    $num_mes = $date->format('d');
                    $num_semana = intdiv($num_mes, 7);
                    //                echo 'DOMINGO:'.$num_mes.'=>'.$num_semana.'<br>';
                    $intervalo_plantilla = 'P' . ($dia_week + $num_semana - 1) . 'D';
                } else {
                    $intervalo_plantilla = 'P' . ($dia_week - 1) . 'D';
                }
                $dia_plantilla3->add(new DateInterval($intervalo_plantilla));
                //            echo 'DIA PLANTILLA3: '.$dia_plantilla3->format('d-m-Y').'<br>';

            }

            if (($QTipoPlantilla == PlantillaConfig::PLANTILLA_MENSUAL_UNO) || ($QTipoPlantilla == PlantillaConfig::PLANTILLA_MENSUAL_TRES)) {
                //            echo 'tipo mensual 1 ó 3 OK<br>';
                $dia_week = $date->format('N');
                $dia_plantilla = new DateTimeLocal(PlantillaConfig::INICIO_MENSUAL_UNO);
                $num_mes = $date->format('d');
                $num_semana = intdiv(($num_mes - 1), 7);
                //            echo 'MENSUAL:'.$num_mes.'=>'.$num_semana.'<br>';
                $intervalo_plantilla = 'P' . ($dia_week + $num_semana * 7 - 1) . 'D';
                $dia_plantilla->add(new DateInterval($intervalo_plantilla));
                //            echo 'DIA PLANTILLA: '.$dia_plantilla->format('d-m-Y').'<br>';
            }

            if ($QTipoPlantilla == PlantillaConfig::PLANTILLA_MENSUAL_TRES) {
                //            echo 'tipo m2 OK<br>';
                $dia_week = $date->format('N');
                $dia_plantilla2 = new DateTimeLocal(PlantillaConfig::INICIO_MENSUAL_DOS);
                $num_mes = $date->format('d');
                $num_semana = intdiv(($num_mes - 1), 7);
                //            echo 'MENSUAL:'.$num_mes.'=>'.$num_semana.'<br>';
                $intervalo_plantilla = 'P' . ($dia_week + $num_semana * 7 - 1) . 'D';
                $dia_plantilla2->add(new DateInterval($intervalo_plantilla));
                //            echo 'DIA PLANTILLA2: '.$dia_plantilla2->format('d-m-Y').'<br>';

                //            echo 'tipo s3 OK<br>';
                $dia_plantilla3 = new DateTimeLocal(PlantillaConfig::INICIO_MENSUAL_TRES);
                $num_mes = $date->format('d');
                $num_semana = intdiv(($num_mes - 1), 7);
                //            echo 'MENSUAL:'.$num_mes.'=>'.$num_semana.'<br>';
                $intervalo_plantilla = 'P' . ($dia_week + $num_semana * 7 - 1) . 'D';
                $dia_plantilla3->add(new DateInterval($intervalo_plantilla));
                //            echo 'DIA PLANTILLA3: '.$dia_plantilla3->format('d-m-Y').'<br>';
            }


            $inicio_dia_plantilla = $dia_plantilla->format('Y-m-d') . ' 00:00:00';
            $fin_dia_plantilla = $dia_plantilla->format('Y-m-d') . ' 23:59:59';
            $aWhere = [
                'id_enc' => $id_enc,
                'tstart' => "'$inicio_dia_plantilla', '$fin_dia_plantilla'",
            ];
            $aOperador = [
                'tstart' => 'BETWEEN',
            ];

            if (($QTipoPlantilla == PlantillaConfig::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantilla == PlantillaConfig::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantilla == PlantillaConfig::PLANTILLA_MENSUAL_TRES)) {
                $inicio_dia_plantilla2 = $dia_plantilla2->format('Y-m-d') . ' 00:00:00';
                $fin_dia_plantilla2 = $dia_plantilla2->format('Y-m-d') . ' 23:59:59';
                $aWhere2 = [
                    'id_enc' => $id_enc,
                    'tstart' => "'$inicio_dia_plantilla2', '$fin_dia_plantilla2'",
                ];
                $aOperador2 = [
                    'tstart' => 'BETWEEN',
                ];
                $inicio_dia_plantilla3 = $dia_plantilla3->format('Y-m-d') . ' 00:00:00';
                $fin_dia_plantilla3 = $dia_plantilla3->format('Y-m-d') . ' 23:59:59';
                $aWhere3 = [
                    'id_enc' => $id_enc,
                    'tstart' => "'$inicio_dia_plantilla3', '$fin_dia_plantilla3'",
                ];
                $aOperador3 = [
                    'tstart' => 'BETWEEN',
                ];
            }

            $EncargoDiaRepository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
            $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);
            //       echo $aWhere['tstart'].$aOperador['tstart'].$aWhere['id_enc'].'<br>';
            if (count($cEncargosDia) > 1) {
                exit(_("sólo debería haber uno"));
            }
            if (count($cEncargosDia) === 1) {
                $oEncargoDia = $cEncargosDia[0];
                $id_nom = $oEncargoDia->getId_nom();
//                echo 'id_nom opcio 1:' . $id_nom . '<br>';
                $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                $hora_fin = $oEncargoDia->getTend()->format('H:i');
                $observ = $oEncargoDia->getObserv();
            }

            //si no hay nadie asignado para ese encargo vacio las variables
            if (count($cEncargosDia) === 0) {
//                echo 'count encargosDia == 0: id_nom a NULL' . $id_nom . '<br>';
                $id_nom = null;
                $hora_ini = '';
                $hora_fin = '';
                $observ = '';
            }


            if ($id_nom != null) {
                $ok_encargo = true;
                //compruebo que no esté fuera
                if (!isset($esta_sacd[$id_nom][$num_dia])) {
                    $esta_sacd[$id_nom][$num_dia] = 1;
                }
//                echo 'id_enc opción 1:' . $id_enc . 'tipo:' . $id_tipo . 'esta: ' . $esta_sacd[$id_nom][$num_dia] . '<br>';
                if ($esta_sacd[$id_nom][$num_dia] > 0) {
//                    echo 'ESTA > 0<br>';
                    if (($id_tipo >= 8100) && ($id_tipo < 8200)) {
                        //compruebo que no tenga otra misa por la mañana
                        //si es de otra zona ya avisa que no está previsto
                        if (!!isset($contador_sacd[$id_nom])) {
                            if ($contador_1a_sacd[$id_nom][$num_dia] > 0) {
                                $ok_encargo = false;
//                                echo 'tendría dos misas por la mañana<br>';
                            }
                        }
                    }
                    if (($id_tipo >= 8200) && ($id_tipo < 8300)) {
                        //compruebo que no tenga tres misas en el día
//                        echo 'contador total: ' . $contador_total_sacd[$id_nom][$num_dia] . '<br>';
                        if ($contador_total_sacd[$id_nom][$num_dia] > 1) {
                            $ok_encargo = false;
//                            echo 'tendría tres misas en el día<br>';
                        }
                    }
                } else {
//                    echo 'está fuera<br>';
                    $ok_encargo = false;
                }
            }

            if ((($QTipoPlantilla == PlantillaConfig::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantilla == PlantillaConfig::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantilla == PlantillaConfig::PLANTILLA_MENSUAL_TRES)) && (!$ok_encargo)) {
                //            echo 'SEGONA OPCIÓ<br>';
                //            echo $aWhere2['tstart'].$aOperador2['tstart'].$aWhere2['id_enc'].'<br>';
                $EncargoDiaRepository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
                $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere2, $aOperador2);

                if (count($cEncargosDia) > 1) {
                    exit(_("sólo debería haber uno"));
                }
                if (count($cEncargosDia) === 1) {
                    $oEncargoDia = $cEncargosDia[0];
                    $id_nom = $oEncargoDia->getId_nom();
                    //                echo 'id_nom segona opcio:'.$id_nom.'<br>';
                    $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                    $hora_fin = $oEncargoDia->getTend()->format('H:i');
                    $observ = $oEncargoDia->getObserv();
                }

                //si no hay nadie asignado para ese encargo vacio las variables
                if (count($cEncargosDia) === 0) {
                    $id_nom = null;
                    //                echo 'id_nom'.$id_nom.'<br>';
                    $hora_ini = '';
                    $hora_fin = '';
                    $observ = '';
                }

                if ($id_nom != null) {
                    $ok_encargo = true;
                    //                echo 'id_enc:'.$id_enc.'tipo:'.$id_tipo.'<br>';
                    //compruebo que no esté fuera
                    if ($esta_sacd[$id_nom][$num_dia] > 0) {
                        if (($id_tipo >= 8100) && ($id_tipo < 8200)) {
                            //compruebo que no tenga otra misa por la mañana
                            //                        echo 'contador 1a: '.$contador_1a_sacd[$id_nom][$num_dia].'<br>';
                            if ($contador_1a_sacd[$id_nom][$num_dia] > 0) {
                                $ok_encargo = false;
                                //                            echo 'tendría dos misas por la mañana<br>';
                            }
                        }
                        if (($id_tipo >= 8200) && ($id_tipo < 8300)) {
                            //compruebo que no tenga tres misas en el día
                            //                        echo 'contador total: '.$contador_total_sacd[$id_nom][$num_dia].'<br>';
                            if ($contador_total_sacd[$id_nom][$num_dia] > 1) {
                                $ok_encargo = false;
                                //                            echo 'tendría tres misas en el día<br>';
                            }
                        }
                    } else {
//                        echo 'está fuera<br>';
                        $ok_encargo = false;
                    }
                }
            }
            if ((($QTipoPlantilla == PlantillaConfig::PLANTILLA_SEMANAL_TRES) || ($QTipoPlantilla == PlantillaConfig::PLANTILLA_DOMINGOS_TRES) || ($QTipoPlantilla == PlantillaConfig::PLANTILLA_MENSUAL_TRES)) && (!$ok_encargo)) {
                //            echo 'TERCERA OPCIÓN<br>';
                $EncargoDiaRepository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
                $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere3, $aOperador3);

                if (count($cEncargosDia) > 1) {
                    exit(_("sólo debería haber uno"));
                }
                if (count($cEncargosDia) === 1) {
                    $oEncargoDia = $cEncargosDia[0];
                    $id_nom = $oEncargoDia->getId_nom();
                    //                echo 'id_nom tercera opcio:'.$id_nom.'<br>';
                    $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                    $hora_fin = $oEncargoDia->getTend()->format('H:i');
                    $observ = $oEncargoDia->getObserv();
                }

                //si no hay nadie asignado para ese encargo vacio las variables
                if (count($cEncargosDia) === 0) {
                    $id_nom = null;
                    //                echo 'id_nom'.$id_nom.'<br>';
                    $hora_ini = '';
                    $hora_fin = '';
                    $observ = '';
                }


                if ($id_nom != null) {
                    $ok_encargo = true;
                    //                echo 'id_enc:'.$id_enc.'tipo:'.$id_tipo.'<br>';
                    //compruebo que no esté fuera
                    if ($esta_sacd[$id_nom][$num_dia] > 0) {
                        if (($id_tipo >= 8100) && ($id_tipo < 8200)) {
                            //compruebo que no tenga otra misa por la mañana
                            //                       echo 'contador 1a: '.$contador_1a_sacd[$id_nom][$num_dia].'<br>';
                            if ($contador_1a_sacd[$id_nom][$num_dia] > 0) {
                                $ok_encargo = false;
                                //                           echo 'tendría dos misas por la mañana<br>';
                            }
                        }
                        if (($id_tipo >= 8200) && ($id_tipo < 8300)) {
                            //compruebo que no tenga tres misas en el día
                            //                       echo 'contador total: '.$contador_total_sacd[$id_nom][$num_dia].'<br>';
                            if ($contador_total_sacd[$id_nom][$num_dia] > 1) {
                                $ok_encargo = false;
                                //                           echo 'tendría tres misas en el día<br>';
                            }
                        }
                    } else {
                        //                   echo 'está fuera<br>';
                        $ok_encargo = false;
                    }
                }
            }
            if ($ok_encargo) {
                //echo 'OOOKKK_ENCARGO<br>';
                $oEncargoDia = new EncargoDia();
                $Uuid = new EncargoDiaId(RamseyUuid::uuid4()->toString());
                $oEncargoDia->setUuid_item($Uuid);
                $oEncargoDia->setId_nom($id_nom);
                $tstart = new EncargoDiaTstart($num_dia, $hora_ini);
                $oEncargoDia->setTstart($tstart);

                $tend = new EncargoDiaTend($num_dia, $hora_fin);
                $oEncargoDia->setTend($tend);

                if (isset($observ)) {
                    $oEncargoDia->setObserv($observ);
                }
                $oEncargoDia->setId_enc($id_enc);
                if ($EncargoDiaRepository->Guardar($oEncargoDia) === FALSE) {
                    $error_txt .= $EncargoDiaRepository->getErrorTxt();
                }
//si es de otra zona ya avisa que no está previsto
                if (!!isset($contador_sacd[$id_nom])) {

                    if (($id_tipo >= 8100) && ($id_tipo < 8200)) {
//                        echo 'Missa a 1a<br>';
                        $contador_1a_sacd[$id_nom][$num_dia]++;
                        $contador_total_sacd[$id_nom][$num_dia]++;
                    }
                    if (($id_tipo >= 8200) && ($id_tipo < 8300)) {
//                        echo 'Missa durant el dia<br>';
                        $contador_total_sacd[$id_nom][$num_dia]++;
                    }
                }
            } else {
                //si no hay nadie asignado para ese encargo vacío las variables
                if (count($cEncargosDia) === 0) {
                    $id_nom = null;
                    //                echo 'id_nom'.$id_nom.'<br>';
                    $hora_ini = '';
                    $hora_fin = '';
                    $observ = '';
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
                exit(_("sólo debería haber uno"));
            }

            if (count($cEncargosDia) === 1) {
                $oEncargoDia = $cEncargosDia[0];
                $id_nom = $oEncargoDia->getId_nom();
                $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                if ($hora_ini == '00:00')
                    $hora_ini = '';
                $iniciales = $InicialesSacdService->obtenerIniciales($id_nom);
                $color = '';
                if (!isset($esta_sacd[$id_nom]))
                    $color = 'verdeclaro';
                $meta_dia["$num_dia"] = [
                    "uuid_item" => $oEncargoDia->getUuid_item()->value(),
                    "color" => $color,
                    "key" => "$id_nom#$iniciales",
                    "tstart" => $oEncargoDia->getTstart()->getHora(),
                    "tend" => $oEncargoDia->getTend()->getHora(),
                    "observ" => $oEncargoDia->getObserv(),
                    "id_enc" => $id_enc,
                ];
                //                echo 'contador 1a: '.$contador_1a_sacd[$id_nom][$num_dia].'<br>';

                // añadir '*' si tiene observaciones
                $iniciales .= empty($oEncargoDia->getObserv()) ? '' : '*';
                $data_cols["$num_dia"] = $iniciales . " " . $hora_ini;
            }
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


$a_campos = ['oPosicion' => $oPosicion,
    'json_columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
    'array_h' => $array_h,
];

$oView = new ViewTwig('misas/controller');
echo $oView->render('ver_cuadricula_zona.html.twig', $a_campos);
