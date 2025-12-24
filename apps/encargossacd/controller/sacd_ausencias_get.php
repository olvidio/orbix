<?php

use core\ViewTwig;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use web\Hash;

/**
 * Esta página muestra las ausencias de un sacd.
 *
 * @package    delegacion
 * @subpackage    des
 * @author    Daniel Serrabou
 * @since        28/03/07.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
//
$oPosicion->recordar();

$Qhistorial = (integer)filter_input(INPUT_POST, 'historial');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qfiltro_sacd = (integer)filter_input(INPUT_POST, 'filtro_sacd');

$hoy = date('Y-m-d');
// tipos de actividades personales y stgr:
$EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
$aWhere = [];
$aOperador = [];
$aWhere['id_tipo_enc'] = '(7|4)...';
$aOperador['id_tipo_enc'] = '~';
$aWhere['_ordre'] = 'id_tipo_enc';
$cEncargos = $EncargoRepository->getEncargos($aWhere, $aOperador);

$array_tipo_ausencias = [];
foreach ($cEncargos as $oEncargo) {
    $array_tipo_ausencias[$oEncargo->getId_enc()] = $oEncargo->getDesc_enc();
}

/* busco los datos del encargo que se tengan */
$EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
$aWhereP = [];
$aOperadorP = [];
if ($Qhistorial === 1) {
    $aWhereP['id_nom'] = $Qid_nom;
    $aWhereP['_ordre'] = 'f_ini';
} else {
    $aWhereP['id_nom'] = $Qid_nom;
    $aWhereP['f_ini'] = $hoy;
    $aOperadorP['f_ini'] = '>=';
    $aWhereP['_ordre'] = 'f_ini';
}
$cEncargosSacd = $EncargoSacdRepository->getEncargosSacd($aWhereP, $aOperadorP);
$i = 0;
$id_enc = [];
$id_tipo_enc = [];
$desc_enc = [];
$id_item = [];
$inicio = [];
$fin = [];
$dedic_m = [];
$dedic_t = [];
$dedic_v = [];
$EncargoSacdHorarioRepository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);
foreach ($cEncargosSacd as $oEncargoSacd) {
    $id_enc[$i] = $oEncargoSacd->getId_enc();
    // Encargo
    $oEncargo = $EncargoRepository->findById($id_enc[$i]);
    $id_tipo_enc[$i] = $oEncargo->getId_tipo_enc();
    // mirar que sea ausencia: id_tipo_enc = 4|7
    if (!preg_match('/[74]/', $id_tipo_enc[$i])) {
        continue;
    }
    $desc_enc[$i] = $oEncargo->getDesc_enc();
    //tarea sacd
    $id_item[$i] = $oEncargoSacd->getId_item();
    $inicio[$i] = $oEncargoSacd->getF_ini()->getFromLocal();
    $fin[$i] = $oEncargoSacd->getF_fin()->getFromLocal();

    // horario
    $aWhereH = [];
    $aOperadorH = [];
    if ($Qhistorial === 1) {
        $aWhereH['id_enc'] = $id_enc[$i];
        $aWhereH['id_nom'] = $Qid_nom;
        $cHorarios = $EncargoSacdHorarioRepository->getEncargoSacdHorarios($aWhereH);
    } else {
        $aWhereH['id_enc'] = $id_enc[$i];
        $aWhereH['id_nom'] = $Qid_nom;
        // con fecha fin > hoy
        $aWhereH['f_fin'] = "'$hoy'";
        $aOperadorH['f_fin'] = '>';
        $cHorarios_1 = $EncargoSacdHorarioRepository->getEncargoSacdHorarios($aWhereH, $aOperadorH);
        // con fecha fin null
        $aWhereH['f_fin'] = "";
        $aOperadorH['f_fin'] = 'IS NULL';
        $cHorarios_2 = $EncargoSacdHorarioRepository->getEncargoSacdHorarios($aWhereH, $aOperadorH);
        $cHorarios = $cHorarios_1 + $cHorarios_2;
    }
    foreach ($cHorarios as $oHorario) {
        switch ($oHorario->getDia_ref()) {
            case "m":
                $dedic_m[$i] = $oHorario->getDia_inc();
                break;
            case "t":
                $dedic_t[$i] = $oHorario->getDia_inc();
                break;
            case "v":
                $dedic_v[$i] = $oHorario->getDia_inc();
                break;
        }
    }
    $i++;
}
$enc_num = $i;

$a_cosas = [
    'id_nom' => $Qid_nom,
    'filtro_sacd' => $Qfiltro_sacd,
    'historial' => 1,
];
$go_to = Hash::link('des/tareas/sacd_ausencias_get.php?' . http_build_query($a_cosas));
//$go_to="des/tareas/sacd_ausencias_get.php?id_nom=".$Qid_nom."&filtro_sacd=$Qfiltro_sacd&historial=1";
$lnk_historia = "<span class='link' onclick=\"fnjs_update_div('#ficha','$go_to');\">" . _("ver anteriores") . "</span>";


$url_update = "apps/encargossacd/controller/sacd_ausencias_update.php";
$oHash = new Hash();
$aCamposHidden = [
    "enc_num" => $enc_num,
    "id_nom" => $Qid_nom,
    "filtro_sacd" => $Qfiltro_sacd,
];
$oHash->setUrl($url_update);
$campos_form = 'id_item!id_enc!fin!inicio';
$oHash->setCamposForm($campos_form);
$oHash->setcamposNo('enc_num!id_item!refresh!mas');
$oHash->setArrayCamposHidden($aCamposHidden);

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_update' => $url_update,
    'lnk_historia' => $lnk_historia,
    'enc_num' => $enc_num,
    'id_tipo_enc' => $id_tipo_enc,
    'id_enc' => $id_enc,
    'id_item' => $id_item,
    'desc_enc' => $desc_enc,
    'inicio' => $inicio,
    'fin' => $fin,
    'array_tipo_ausencias' => $array_tipo_ausencias,
];

$oView = new ViewTwig('encargossacd/controller');
$oView->renderizar('sacd_ausencias_get.html.twig', $a_campos);
