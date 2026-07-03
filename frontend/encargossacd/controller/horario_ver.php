<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\encargossacd\helpers\EncargossacdPostInput;
use frontend\encargossacd\helpers\EncargossacdPayload;
use frontend\shared\helpers\ListNavSupport;

/**
 * Formulario horario de encargo. Datos: `/src/encargossacd/horario_ver_data`
 * (ver {@see \src\encargossacd\application\EncargoHorarioVerData}).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = EncargossacdPostInput::postInt('refresh');
ListNavSupport::bootRecordar($oPosicion, $Qrefresh);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::buildReturnParametrosFromPost());


$Qmod = EncargossacdPostInput::postString('mod');
$Qid_enc = EncargossacdPostInput::postInt('id_enc');
$Qorigen = EncargossacdPostInput::postString('origen');
$Qdesc_enc = EncargossacdPostInput::postString('desc_enc');
$Qdesc_enc = urldecode($Qdesc_enc);

$id_item_h = EncargossacdPostInput::postSelIdItemH(EncargossacdPostInput::postInt('id_item_h'));

/** @var array<string, mixed> $data */
$data = PostRequest::getDataFromUrl('/src/encargossacd/horario_ver_data', [
    'mod' => $Qmod,
    'id_enc' => $Qid_enc,
    'id_item_h' => $id_item_h,
]);

$horario = EncargossacdPayload::horarioVerFromPayload($data);
$f_ini = $horario['f_ini'];
$f_fin = $horario['f_fin'];
$dia_ref = $horario['dia_ref'];
$dia_num = $horario['dia_num'];
$mas_menos = $horario['mas_menos'];
$dia_inc = $horario['dia_inc'];
$h_ini = $horario['h_ini'];
$h_fin = $horario['h_fin'];
$n_sacd = $horario['n_sacd'];
$mes = $horario['mes'];
$id_item_h = $horario['id_item_h'];
$dia = $horario['dia'];
$opciones_dia_semana = $horario['opciones_dia_semana'];
$opciones_dia_ref = $horario['opciones_dia_ref'];
$opciones_ordinales = $horario['opciones_ordinales'];

$titulo = _("horario de") . ": " . $Qdesc_enc;

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

$oDesplOrd = new Desplegable();
$oDesplOrd->setNombre('dia_num');
$oDesplOrd->setBlanco('t');
$oDesplOrd->setOpciones($opciones_ordinales);
$oDesplOrd->setOpcion_sel($dia_num);

$oDesplRef = new Desplegable();
$oDesplRef->setNombre('dia_ref');
$oDesplRef->setBlanco('t');
$oDesplRef->setOpciones($opciones_dia_ref);
$oDesplRef->setOpcion_sel($dia_ref);

$url_actualizar = 'frontend/encargossacd/controller/encargo_ver.php';
$oHash = new HashFront();
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

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('horario_ver.phtml', $a_campos);
