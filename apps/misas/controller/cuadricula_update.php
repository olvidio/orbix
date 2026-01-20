<?php

// INICIO Cabecera global de URL de controlador *********************************

use Illuminate\Http\JsonResponse;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoDiaId;
use src\misas\domain\value_objects\EncargoDiaTend;
use src\misas\domain\value_objects\EncargoDiaTstart;
use src\misas\domain\value_objects\PlantillaConfig;
use src\shared\domain\value_objects\DateTimeLocal;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use web\TiposActividades;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Quuid_item = (string)filter_input(INPUT_POST, 'uuid_item');
$Qkey = (string)filter_input(INPUT_POST, 'key');
$Qtstart = (string)filter_input(INPUT_POST, 'tstart');
$Qtend = (string)filter_input(INPUT_POST, 'tend');
$Qobserv = (string)filter_input(INPUT_POST, 'observ');
$Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
$Qdia = (string)filter_input(INPUT_POST, 'dia');
$QTipoPlantilla = (string)filter_input(INPUT_POST, 'tipo_plantilla');
$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');

$sdia = $Qdia;
$dia = DateTimeLocal::createFromLocal($Qdia);
$dia_iso = $dia->getIso();
//echo 'Qkey'.$Qkey.'<br>';
$comprobacion = '';

$error_txt = '';

if (empty($Quuid_item)) {
    exit("Error: falta el id_item");
}
$Uuid = new EncargoDiaId($Quuid_item);

$EncargoDiaRepository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
$oEncargoDia = $EncargoDiaRepository->findById($Uuid);
if ($oEncargoDia === null) {
    $oEncargoDia = new EncargoDia();
    $oEncargoDia->setUuid_item($Uuid);
    $oEncargoDia->setId_enc($Qid_enc);
}
$id_sacd_anterior = $oEncargoDia->getId_nom();
//echo 'sacd_anterior:'.$id_sacd_anterior.'<br>';
$estado = $oEncargoDia->getStatus();
$zona = $Qid_zona;
$color_misa = '';
if (trim($QTipoPlantilla) === PlantillaConfig::PLAN_DE_MISAS) {
    if ($estado === EncargoDia::STATUS_PROPUESTA) {
        $color_misa = 'rojoclaro';
    }
    if ($estado === EncargoDia::STATUS_COMUNICADO_SACD) {
        $color_misa = 'amarilloclaro';
    }
    if ($estado === EncargoDia::STATUS_COMUNICADO_CTR) {
        $color_misa = 'verdeclaro';
    }
}

$id_nom = '';
$flag_borrado = FALSE;
if (empty($Qkey)) { // no hay ningún sacd
    if ($EncargoDiaRepository->Eliminar($oEncargoDia) === FALSE) {
        $error_txt .= $EncargoDiaRepository->getErrorTxt();
    }
    $flag_borrado = TRUE;
} else {
    $porciones = explode("#", $Qkey);
    $iniciales = $porciones[0];
    $id_nom = $porciones[1];
//    echo 'id nom: '.$id_nom.'<br>';
    $oEncargoDia->setId_nom($id_nom);

//    echo 'error_txt: '.$error_txt;

    $QTstart = new EncargoDiaTstart($Qdia, $Qtstart);
    $oEncargoDia->setTstart($QTstart);

    $QTend = new EncargoDiaTend($Qdia, $Qtend);
    $oEncargoDia->setTend($QTend);

    $oEncargoDia->setObserv($Qobserv);


    if ($EncargoDiaRepository->Guardar($oEncargoDia) === FALSE) {
        $error_txt .= $EncargoDiaRepository->getErrorTxt();
    }
}
$aWhere = [];
$aWhere['id_zona'] = $zona;
$aOperador = [];
$ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
$cZonaSacd = $ZonaSacdRepository->getZonasSacds($aWhere, $aOperador);
$InicialesSacdService = $GLOBALS['container']->get(InicialesSacdService::class);
foreach ($cZonaSacd as $oZonaSacd) {
    $data_cols = [];
    $id_nom_aux = $oZonaSacd->getId_nom();
    $nombre_sacd = $InicialesSacdService->obtenerNombreConIniciales($id_nom_aux);
    $iniciales = $InicialesSacdService->obtenerIniciales($id_nom_aux);
    $key = $id_nom_aux;
    $lista_sacd[$key] = $nombre_sacd;
    $esta_en_zona[$key] = array('', $oZonaSacd->isDw1(), $oZonaSacd->isDw2(), $oZonaSacd->isDw3(), $oZonaSacd->isDw4(), $oZonaSacd->isDw5(), $oZonaSacd->isDw6(), $oZonaSacd->isDw7());
}

$esta_sacd_anterior = 1;
$donde_esta_sacd_anterior = '';
$ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
$EncargoSacdHorarioRepository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);
if (!empty($id_sacd_anterior)) {
//    echo '--------------SACD ANTERIOR<br>';
    $aWhereAct = [];
    $aOperadorAct = [];
    $aWhereAct['f_ini'] = $dia_iso;
    $aOperadorAct['f_ini'] = '<=';
    $aWhereAct['f_fin'] = $dia_iso;
    $aOperadorAct['f_fin'] = '>=';
    $aWhereAct['status'] = StatusId::ACTUAL;
    $aWhere = ['id_nom' => $id_sacd_anterior];
    $aOperador = [];

    $cAsistentes = $ActividadCargoRepository->getAsistenteCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);

    $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
    $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
    $EncargoTipoRepository = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);
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
        $nom_activ = $oActividad->getNom_activ();
        $oTipoActividad = new TiposActividades($id_tipo_activ);
        $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
        $nom_llarg = $nom_activ;

//        echo $nom_llarg.'<br>';
//        echo $id_nom.'<br>';
        if (isset($esta_sacd_anterior)) {
            if ($esta_sacd_anterior === 1) {
                $esta_sacd_anterior = 2;
            }
        }
        $donde_esta_sacd_anterior = $nom_llarg;
    }
    // ++++++++++++++ Añado las ausencias +++++++++++++++
    $aWhereE = [];
    $aOperadorE = [];
    $aWhereE['id_nom'] = $id_sacd_anterior;
    $aWhereE['f_ini'] = "'$dia_iso'";
    $aOperadorE['f_ini'] = '<=';
    $aWhereE['f_fin'] = "'$dia_iso'";
    $aOperadorE['f_fin'] = '>=';
    $cAusencias = $EncargoSacdHorarioRepository->getEncargoSacdHorarios($aWhereE, $aOperadorE);
    foreach ($cAusencias as $oTareaHorarioSacd) {
        $id_enc = $oTareaHorarioSacd->getId_enc();
        $oF_ini = $oTareaHorarioSacd->getF_ini();
        $oF_fin = $oTareaHorarioSacd->getF_fin();

        $oEncargo = $EncargoRepository->findById($id_enc);
        $id_tipo_enc = $oEncargo->getId_tipo_enc();
        $id = (string)$id_tipo_enc;
        if ((int)$id[0] !== 7 && (int)$id[0] !== 4) {
            continue;
        }

        $ini = (string)$oF_ini->getFromLocal();
        $fi = (string)$oF_fin->getFromLocal();

        $nom_llarg = $oEncargo->getDesc_enc();
        $nom_curt = ($nom_llarg[0] === 'A') ? 'a' : 'x';
        if ($ini !== $fi) {
            $nom_llarg .= " ($ini-$fi)";
        } else {
            $nom_llarg .= " ($ini)";
        }

//                echo $nom_llarg;
        if (isset($esta_sacd_anterior)) {
            if ($esta_sacd_anterior === 1) {
                $esta_sacd_anterior = 2;
            }
        }
        $donde_esta_sacd_anterior = $nom_llarg;
    }
}
$esta_sacd = 1;
$donde_esta_sacd = '';

//echo 'NUEVO SACD------------<br>';
if (!empty($id_nom)) {
    $aWhereAct = [];
    $aOperadorAct = [];
    $aWhereAct['f_ini'] = $dia_iso;
    $aOperadorAct['f_ini'] = '<=';
    $aWhereAct['f_fin'] = $dia_iso;
    $aOperadorAct['f_fin'] = '>=';
    $aWhereAct['status'] = StatusId::ACTUAL;
    $aWhere = ['id_nom' => $id_nom];
    $aOperador = [];

    $cAsistentes = $ActividadCargoRepository->getAsistenteCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);

    $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
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
        $nom_activ = $oActividad->getNom_activ();
        $oTipoActividad = new TiposActividades($id_tipo_activ);
        $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
        $nom_llarg = $nom_activ;

//        echo $nom_llarg.'<br>';
//        echo $id_nom.'<br>';
        if (isset($esta_sacd)) {
            if ($esta_sacd === 1) {
                $esta_sacd = 2;
            }
        }
        $donde_esta_sacd = $nom_llarg;
    }
    // ++++++++++++++ Añado las ausencias +++++++++++++++
    $aWhereE = [];
    $aOperadorE = [];
    $aWhereE['id_nom'] = $id_nom;
    $aWhereE['f_ini'] = "'$dia_iso'";
    $aOperadorE['f_ini'] = '<=';
    $aWhereE['f_fin'] = "'$dia_iso'";
    $aOperadorE['f_fin'] = '>=';
    $cAusencias = $EncargoSacdHorarioRepository->getEncargoSacdHorarios($aWhereE, $aOperadorE);
    foreach ($cAusencias as $oTareaHorarioSacd) {
        $id_enc = $oTareaHorarioSacd->getId_enc();
        $oF_ini = $oTareaHorarioSacd->getF_ini();
        $oF_fin = $oTareaHorarioSacd->getF_fin();

        $oEncargo = $EncargoRepository->findById($id_enc);
        $id_tipo_enc = $oEncargo->getId_tipo_enc();
        $id = (string)$id_tipo_enc;
        if ((int)$id[0] !== 7 && (int)$id[0] !== 4) {
            continue;
        }

        $ini = (string)$oF_ini->getFromLocal();
        $fi = (string)$oF_fin->getFromLocal();

        $nom_llarg = $oEncargo->getDesc_enc();
        $nom_curt = ($nom_llarg[0] === 'A') ? 'a' : 'x';
        if ($ini !== $fi) {
            $nom_llarg .= " ($ini-$fi)";
        } else {
            $nom_llarg .= " ($ini)";
        }

//                echo $nom_llarg;
        if (isset($esta_sacd)) {
            if ($esta_sacd === 1) {
                $esta_sacd = 2;
            }
        }
        $donde_esta_sacd = $nom_llarg;
    }
}

//            echo 'conto misses SACD ANTERIOR---------<br>';
$texto_anterior = '';
$texto_sacd_anterior = '--';
$color_fondo_anterior = 'verdeclaro';

$inicio_dia = $Qdia . ' 00:00:00';
$fin_dia = $Qdia . ' 23:59:59';
$num_dia = $Qdia;
$dws = $dia->format('N');

if (!empty($id_sacd_anterior)) {
//echo $inicio_dia.'-'.$fin_dia.'<br>';
//echo 'id nom: '.$id_nom.'<br>';

    $aWhere = [
        'id_nom' => $id_sacd_anterior,
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
        if ((int)substr($id_tipo_enc, 1, 1) === 1) {
            $misas_dia++;
            $misas_1a_hora++;
            if ($zona === $id_zona_enc) {
                $misas_dia_zona++;
                $misas_1a_hora_zona++;
            }
        }
        if ((int)substr($id_tipo_enc, 1, 1) === 2) {
            $misas_dia++;
            if ($zona === $id_zona_enc) {
                $misas_dia_zona++;
            }
        }
//            echo 'MD:'.$misas_dia.'M1:'.$misas_1a_hora.'<br>';
    }
//        echo ' fin foreach MD:'.$misas_dia.'M1:'.$misas_1a_hora.'<br>';
//echo 'num dia..dws..esta en zona: '.$num_dia.'-'.$dws.'='.$esta_en_zona[$id_sacd_anterior][$dws].'<br>';
    $esta_en_zona_anterior = $esta_en_zona[$id_sacd_anterior][$dws];
    $color_fondo_anterior = 'verdeclaro';
    $texto_anterior = '';
    if ($misas_dia > 2) {
        $texto_anterior = 'Este día tiene más de dos Misas';
        $color_fondo_anterior = 'rojo';
    }
    if ($misas_dia === 2) {
        $texto_anterior = 'Este día tiene dos Misas';
        $color_fondo_anterior = 'amarillo';
    }
    if (($misas_dia === 0) && ($esta_en_zona_anterior)) {
        $texto_anterior = 'Este día no tiene ninguna Misa';
        $color_fondo_anterior = 'verde';
    }
    if (($misas_dia === 0) && (!$esta_en_zona_anterior)) {
        $texto_anterior = 'Este día no tiene ninguna Misa';
        $color_fondo_anterior = 'azulclaro';
    }
    if ($misas_1a_hora === 2) {
        $texto_anterior = 'Tiene dos Misas a primera hora';
        $color_fondo_anterior = 'rojo';
    }


    if ($esta_en_zona_anterior) {
        $texto_sacd_anterior = 'SI';
    } else {
        if ($misas_1a_hora_zona > 0) {
            $color_fondo_anterior = 'rojo';
            $texto_anterior = 'No está en la zona y tiene Misa a primera hora';
        }
        $texto_sacd_anterior = 'NO';
    }
    if ($esta_sacd_anterior < 1) {
//            echo $id_nom.' está en '.$donde_esta_sacd[$id_nom][$num_dia].$num_dia.'<br>';
        if ($misas_1a_hora_zona > 0) {
//                echo '1a: '.$misas_1a_hora.'<br>';
            $color_fondo_anterior = 'rojo';
        }
        $texto_anterior = 'Está en ' . $donde_esta_sacd;
        $texto_sacd_anterior = '--';
    }
//        $inicio_dia = $Qdia.' 00:00:00';
//        $fin_dia = $Qdia.' 23:59:59';

    $comprobacion = 'MD:' . $misas_dia . ' M1h:' . $misas_1a_hora . 'MDZ:' . $misas_dia_zona . 'Z:' . $esta_en_zona_anterior;
}
$texto = '';
$texto_sacd = '--';
$color_fondo = 'verdeclaro';

//echo 'id_nom: '.$id_nom.'<br>';
//echo 'CONTO MISSES NOU MOSSEN------<br>';
if (!empty($id_nom)) {

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
        if ((int)substr($id_tipo_enc, 1, 1) === 1) {
            $misas_dia++;
            $misas_1a_hora++;
            if ($zona === $id_zona_enc) {
                $misas_dia_zona++;
                $misas_1a_hora_zona++;
            }
        }
        if ((int)substr($id_tipo_enc, 1, 1) === 2) {
            $misas_dia++;
            if ($zona === $id_zona_enc) {
                $misas_dia_zona++;
            }
        }
//            echo 'MD:'.$misas_dia.'M1:'.$misas_1a_hora.'<br>';
    }


    //       echo $misas_dia.$misas_1a_hora.'<br>';
//        $num_dia = $date->format('Y-m-d');
//        $dws = $dia_week_sacd[$num_dia];
//echo $num_dia.'-'.$dws.'='.$esta_en_zona[$dws].'<br>';
//echo 'num dia..dws..esta en zona: '.$num_dia.'-'.$dws.'='.$esta_en_zona[$id_nom][$dws].'<br>';
    $esta_en_zona_nuevo = $esta_en_zona[$id_nom][$dws];
//        $texto='';
    if ($misas_dia > 2) {
        $texto = 'Este día tiene más de dos Misas';
        $color_fondo = 'rojo';
    }
    if ($misas_dia === 2) {
        $texto = 'Este día tiene dos Misas';
        $color_fondo = 'amarillo';
    }
    if (($misas_dia === 0) && ($esta_en_zona_nuevo)) {
        $texto = 'Este día no tiene ninguna Misa';
        $color_fondo = 'verde';
    }
    if (($misas_dia === 0) && (!$esta_en_zona_nuevo)) {
        $texto = 'Este día no tiene ninguna Misa';
        $color_fondo = 'azulclaro';
    }
    if ($misas_1a_hora === 2) {
        $texto = 'Tiene dos Misas a primera hora';
        $color_fondo = 'rojo';
    }


    if ($esta_en_zona_nuevo) {
//            $data_cols[$num_dia]=$misas_dia*10+$misas_1a_hora;
        $texto_sacd = 'SI';
//            $data_cols[$num_dia] = 'SI';
    } else {
//            $data_cols[$num_dia]=$misas_dia*10+$misas_1a_hora;
        if ($misas_1a_hora_zona > 0) {
            $color_fondo = 'rojo';
            $texto = 'No está en la zona y tiene Misa a primera hora';
        }
        $texto_sacd = 'NO';
//            $data_cols[$num_dia] = 'NO';
    }
    if ($esta_sacd < 1) {
//            echo $id_nom.' está en '.$donde_esta_sacd[$id_nom][$num_dia].$num_dia.'<br>';
        if ($misas_1a_hora_zona > 0) {
//                echo '1a: '.$misas_1a_hora.'<br>';
            $color_fondo = 'rojo';
        }
        $texto = 'Está en ' . $donde_esta_sacd[$id_nom][$num_dia];
        $texto_sacd = '--';
//            $data_cols[$num_dia] = '--';
    }
    $comprobacion .= '----- MD:' . $misas_dia . ' M1h:' . $misas_1a_hora . 'MDZ:' . $misas_dia_zona . 'Z:' . $esta_en_zona_nuevo;
}

if (empty($error_txt)) {
    $jsondata['success'] = true;
    $a_meta = [
        'color_misa' => $color_misa,
        'id_sacd_anterior' => $id_sacd_anterior,
        'texto_anterior' => $texto_anterior,
        'color_fondo_anterior' => $color_fondo_anterior,
        'texto_sacd_anterior' => $texto_sacd_anterior,
        'texto' => $texto,
        'color_fondo' => $color_fondo,
        'texto_sacd' => $texto_sacd,
        'comprobacion' => $comprobacion,
    ];
    $jsondata['meta'] = $a_meta;
} else {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = 'ERROR: ' . $error_txt;
}

(new JsonResponse($jsondata))->send();
exit();
