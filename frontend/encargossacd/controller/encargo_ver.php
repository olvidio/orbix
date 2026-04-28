<?php

use frontend\encargossacd\model\DesplCentros;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;

/**
 * Ficha de alta/edicion de un encargo. Los datos iniciales (resolucion del
 * encargo, grupos posibles, tipos, secciones, zonas y locales) vienen de
 * {@see \src\encargossacd\application\EncargoVerData} a traves de
 * `/src/encargossacd/encargo_ver_data`. Aqui solo armamos los desplegables
 * (`frontend\shared\web\Desplegable`) y las URLs/hashes para el JS de la vista.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $Qid_enc = (int)strtok((string)$a_sel[0], '#');
} else {
    $Qid_enc = (int)filter_input(INPUT_POST, 'id_enc');
}

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_tipo_enc = (int)filter_input(INPUT_POST, 'id_tipo_enc');
$Qgrupo = (string)filter_input(INPUT_POST, 'grupo');
$Qfiltro_ctr = (string)filter_input(INPUT_POST, 'filtro_ctr');
$Qdesc_enc = (string)filter_input(INPUT_POST, 'desc_enc');
$Qdesc_lugar = (string)filter_input(INPUT_POST, 'desc_lugar');
$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');

/** @var array<string, mixed> $data */
$data = PostRequest::getDataFromUrl('/src/encargossacd/encargo_ver_data', [
    'que' => $Qque,
    'id_enc' => $Qid_enc,
    'id_tipo_enc' => $Qid_tipo_enc,
    'grupo' => $Qgrupo,
    'filtro_ctr' => $Qfiltro_ctr,
    'desc_enc' => $Qdesc_enc,
    'desc_lugar' => $Qdesc_lugar,
    'id_zona' => $Qid_zona,
]);

$Qque = (string)($data['que'] ?? $Qque);
$Qid_enc = (int)($data['id_enc'] ?? $Qid_enc);
$Qid_tipo_enc = (int)($data['id_tipo_enc'] ?? $Qid_tipo_enc);
$Qgrupo = (string)($data['grupo'] ?? $Qgrupo);
$Qfiltro_ctr = (string)($data['filtro_ctr'] ?? $Qfiltro_ctr);
$Qdesc_enc = (string)($data['desc_enc'] ?? $Qdesc_enc);
$Qdesc_lugar = (string)($data['desc_lugar'] ?? $Qdesc_lugar);
$idioma_enc = (string)($data['idioma_enc'] ?? '');
$Qid_ubi = (int)($data['id_ubi'] ?? 0);
$Qid_zona = (int)($data['id_zona'] ?? $Qid_zona);
$grupo_posibles = is_array($data['grupo_posibles'] ?? null) ? $data['grupo_posibles'] : [];
$posibles_encargo_tipo = is_array($data['posibles_encargo_tipo'] ?? null) ? $data['posibles_encargo_tipo'] : [];
$opciones_seccion = is_array($data['opciones_seccion'] ?? null) ? $data['opciones_seccion'] : [];
$opciones_zonas = is_array($data['opciones_zonas'] ?? null) ? $data['opciones_zonas'] : [];
$opciones_locales = is_array($data['opciones_locales'] ?? null) ? $data['opciones_locales'] : [];

$oDesplGrupos = new Desplegable();
$oDesplGrupos->setNombre('grupo');
$oDesplGrupos->setOpciones($grupo_posibles);
$oDesplGrupos->setOpcion_sel($Qgrupo);
$oDesplGrupos->setBlanco(1);
$oDesplGrupos->setAction("fnjs_lst_tipo_enc();");

$oDesplNoms = new Desplegable();
if (!empty($posibles_encargo_tipo)) {
    $oDesplNoms->setNombre('id_tipo_enc');
    $oDesplNoms->setOpciones($posibles_encargo_tipo);
    $oDesplNoms->setOpcion_sel($Qid_tipo_enc);
    $oDesplNoms->setBlanco('t');
} else {
    $oDesplNoms->setOpciones([]);
}

$oDesplGrupoCtrs = new Desplegable();
$oDesplGrupoCtrs->setNombre('filtro_ctr');
$oDesplGrupoCtrs->setOpciones($opciones_seccion);
$oDesplGrupoCtrs->setOpcion_sel($Qfiltro_ctr);
$oDesplGrupoCtrs->setBlanco(1);
$oDesplGrupoCtrs->setAction("fnjs_lista_ctrs();");

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($opciones_zonas);
$oDesplZonas->setBlanco(false);
$oDesplZonas->setNombre('id_zona_sel');
$oDesplZonas->setAction('fnjs_lista_ctrs_por_zona()');
if ($Qid_zona !== 0) {
    $oDesplZonas->setOpcion_sel($Qid_zona);
}

$oDesplCtrs = DesplCentros::build((int)$Qfiltro_ctr, $Qid_ubi, $Qid_zona);

$oDesplIdiomas = new Desplegable('idioma_enc', $opciones_locales, $idioma_enc, true);

$url_actualizar = 'frontend/encargossacd/controller/encargo_ver.php';
$apiBase = AppUrlConfig::getApiBaseUrl();

$oHashAct = new HashFront();
$oHashAct->setUrl($url_actualizar);
$oHashAct->setCamposForm('desc_enc!desc_lugar!filtro_ctr!grupo!id_tipo_enc!id_zona!idioma_enc');
$oHashAct->setcamposNo('id_zona!id_zona_sel!lst_ctrs!refresh');
$oHashAct->setArrayCamposHidden([
    'que' => $Qque,
    'id_enc' => $Qid_enc,
    'id_zona' => $Qid_zona,
]);

$url_zona = $apiBase . '/src/encargossacd/zonas_get_select_data';
$oHashZona = new HashFront();
$oHashZona->setUrl($url_zona);
$oHashZona->setCamposForm('id_zona');
$h_zona = $oHashZona->linkSinValParams();

$url_ctr = $webBase . '/src/encargossacd/ctr_get_select_data';
$oHashCtr = new HashFront();
$oHashCtr->setUrl($url_ctr);
$oHashCtr->setCamposForm('filtro_ctr');
$h_ctr = $oHashCtr->linkSinValParams();
$oHashCtr->setCamposForm('id_zona');
$h_ctr_zona = $oHashCtr->linkSinValParams();

$url_lst_tipo_data = $apiBase . '/src/encargossacd/encargo_lst_tipo_enc_data';
$oHashLstTipo = new HashFront();
$oHashLstTipo->setUrl($url_lst_tipo_data);
$oHashLstTipo->setCamposForm('grupo!id_tipo_enc');
$h_lst_tipo = $oHashLstTipo->linkSinValParams();

$url_encargo_ver_nuevo = $apiBase . '/src/encargossacd/encargo_ver_nuevo';
$oHashEncNuevo = new HashFront();
$oHashEncNuevo->setUrl($url_encargo_ver_nuevo);
$oHashEncNuevo->setCamposForm('desc_enc!desc_lugar!idioma_enc!filtro_ctr!grupo!id_tipo_enc!id_zona!lst_ctrs!observ!nom_tipo!que');
$h_encargo_ver_nuevo = $oHashEncNuevo->linkSinValParams();

$url_encargo_ver_editar = $apiBase . '/src/encargossacd/encargo_ver_editar';
$oHashEncEditar = new HashFront();
$oHashEncEditar->setUrl($url_encargo_ver_editar);
$oHashEncEditar->setCamposForm('desc_enc!desc_lugar!idioma_enc!filtro_ctr!grupo!id_tipo_enc!id_zona!lst_ctrs!observ!nom_tipo!que!id_enc');
$h_encargo_ver_editar = $oHashEncEditar->linkSinValParams();

$txt_btn = $Qque === 'nuevo' ? _("crear encargo") : _("guardar encargo");

$a_campos = [
    'oPosicion' => $oPosicion,
    'url_actualizar' => $url_actualizar,
    'url_zona' => $url_zona,
    'h_zona' => $h_zona,
    'url_ctr' => $url_ctr,
    'h_ctr' => $h_ctr,
    'h_ctr_zona' => $h_ctr_zona,
    'url_lst_tipo_data' => $url_lst_tipo_data,
    'h_lst_tipo' => $h_lst_tipo,
    'url_encargo_ver_nuevo' => $url_encargo_ver_nuevo,
    'h_encargo_ver_nuevo' => $h_encargo_ver_nuevo,
    'url_encargo_ver_editar' => $url_encargo_ver_editar,
    'h_encargo_ver_editar' => $h_encargo_ver_editar,
    'oHash' => $oHashAct,
    'id_enc' => $Qid_enc,
    'id_tipo_enc' => $Qid_tipo_enc,
    'oDesplGrupos' => $oDesplGrupos,
    'oDesplNoms' => $oDesplNoms,
    'oDesplGrupoCtrs' => $oDesplGrupoCtrs,
    'oDesplZonas' => $oDesplZonas,
    'oDesplCtrs' => $oDesplCtrs,
    'oDesplIdiomas' => $oDesplIdiomas,
    'desc_enc' => $Qdesc_enc,
    'desc_lugar' => $Qdesc_lugar,
    'que' => $Qque,
    'txt_btn' => $txt_btn,
    'grupo' => $Qgrupo,
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('encargo_ver.phtml', $a_campos);
