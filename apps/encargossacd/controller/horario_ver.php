<?php

use encargossacd\model\entity\EncargoHorario;
use web\Hash;
use web\Desplegable;
use encargossacd\model\EncargoConstants;
use encargossacd\model\entity\GestorEncargoHorarioExcepcion;

/**
 * Esta página muestra un formulario para crear un horario
 *
 *
 * @package    delegacion
 * @subpackage    encargos
 * @author    Daniel Serrabou
 * @since        24/2/06.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
$Qdesc_enc = (string)filter_input(INPUT_POST, 'desc_enc');
$Qdesc_enc = urldecode($Qdesc_enc);

if ($Qmod != 'nuevo') { //significa que no es nuevo
    $id_item_h = (integer)filter_input(INPUT_POST, 'id_item_h');
    if (!empty($_POST['sel'])) { //vengo de un checkbox
        $id_item_h = strtok($_POST['sel'][0], "#");
    }
    $EncargoHorario = new EncargoHorario($id_item_h);
    $f_ini = $EncargoHorario->getF_ini()->getFromLocal();
    $f_fin = $EncargoHorario->getF_fin()->getFromLocal();
    $dia_ref = $EncargoHorario->getDia_ref();
    $dia_num = $EncargoHorario->getDia_num();
    $mas_menos = $EncargoHorario->getMas_menos();
    $dia_inc = $EncargoHorario->getDia_inc();
    $h_ini = $EncargoHorario->getH_ini();
    $h_fin = $EncargoHorario->getH_fin();
    $n_sacd = $EncargoHorario->getN_sacd();
    $mes = $EncargoHorario->getMes();
} else {
    $id_item_h = '';
    $f_ini = '';
    $f_fin = '';
    $dia_ref = '';
    $dia_num = '';
    $mas_menos = '';
    $dia_inc = '';
    $h_ini = '';
    $h_fin = '';
    $n_sacd = '';
    $mes = '';
}

if (empty($id_item_h)) {
    $titulo = _("nuevo") . " ";
}
$titulo = _("horario de") . ": " . $Qdesc_enc;

$oGesEncagoTipo = new \encargossacd\model\entity\GestorEncargoTipo();
$dia = $oGesEncagoTipo->calcular_dia($mas_menos, $dia_ref, $dia_inc);
$opciones_dia_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
$oDesplDia = new Desplegable();
$oDesplDia->setNombre('dia');
$oDesplDia->setOpciones($opciones_dia_semana);
$oDesplDia->setOpcion_sel($dia);
$oDesplDia->setBlanco('t');

$oDesplMas = new Desplegable();
$oDesplMas->setNombre('mas_menos');
$oDesplMas->setBlanco('t');
$aOpciones = [
    "-" => _("antes del"),
    "+" => _("después del"),
];
$oDesplMas->setOpciones($aOpciones);
$oDesplMas->setOpcion_sel($mas_menos);

$op_ordinales = EncargoConstants::OPCIONES_ORDINALES;
$oDesplOrd = new Desplegable();
$oDesplOrd->setNombre('dia_num');
$oDesplOrd->setBlanco('t');
$oDesplOrd->setOpciones($op_ordinales);
$oDesplOrd->setOpcion_sel($dia_num);

$op_dia_ref = EncargoConstants::OPCIONES_DIA_REF;
$oDesplRef = new Desplegable();
$oDesplRef->setNombre('dia_ref');
$oDesplRef->setBlanco('t');
$oDesplRef->setOpciones($op_dia_ref);
$oDesplRef->setOpcion_sel($dia_ref);

// miro si tinen excepciones:
if (!empty($id_item_h)) {
    $GesEncargoHorarioExcepcion = new GestorEncargoHorarioExcepcion();
    $cEncargoHorarioExcepcion = $GesEncargoHorarioExcepcion->getEncargoHorarioExcepciones(['id_item_h' => $id_item_h]);
    if (empty($cEncargoHorarioExcepcion)) {
        //echo "<input TYPE=\"button\" VALUE=\"".ucfirst(_("generar excepciones"))."\" onclick=\"javascript:fnjs_guardar(3)\"> ";
        //echo "</form>";
    } else {
        //echo "</form>";
        //include("horario_excepcion_select.php");
    }
}

$url_actualizar = 'apps/encargossacd/controller/encargo_ver.php';
$oHash = new Hash();
$aCamposHidden = [
    'mod' => $Qmod,
    'id_enc' => $Qid_enc,
    'id_item_h' => $id_item_h,
    'desc_enc' => $Qdesc_enc,
];

$oHash->setUrl($url_actualizar);
$campos_form = 'desc_enc!dia!dia_inc!dia_num!dia_ref!f_fin!f_ini!h_fin!h_ini!id_enc!id_item_h!mas_menos!mod!n_sacd';
$oHash->setCamposForm($campos_form);
$oHash->setcamposNo('lst_ctrs!refresh');
$oHash->setArrayCamposHidden($aCamposHidden);

if ($Qmod === 'nuevo') {
    $txt_btn = _("crear horario");
} else {
    $txt_btn = _("guardar horario");
}

$a_campos = ['oPosicion' => $oPosicion,
    'titulo' => $titulo,
    'url_actualizar' => $url_actualizar,
    'oHash' => $oHash,
    'id_enc' => $Qid_enc,
    'oDesplDia' => $oDesplDia,
    'oDesplMas' => $oDesplMas,
    'oDesplOrd' => $oDesplOrd,
    'oDesplRef' => $oDesplRef,
    'mod' => $Qmod,
    'f_ini' => $f_ini,
    'f_fin' => $f_fin,
    'h_ini' => $h_ini,
    'h_fin' => $h_fin,
    'n_sacd' => $n_sacd,
    'txt_btn' => $txt_btn,
];

$oView = new core\ViewTwig('encargossacd/controller');
$oView->renderizar('horario_ver.html.twig', $a_campos);