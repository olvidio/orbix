<?php
/**
 * Pantalla: configuracion de avisos por usuario/grupo.
 *
 * Migrada desde `apps/cambios/controller/usuario_avisos_pref.php` siguiendo
 * `refactor.md`. La pantalla consume `/src/cambios/usuario_avisos_pref_form_data`
 * y construye los `Desplegable`/`Hash`/`ActividadTipo` aqui. Los fragmentos
 * que se cargan por AJAX viven en `usuario_avisos_pref_propiedades.php`,
 * `usuario_avisos_pref_condicion.php` y `usuario_avisos_pref_fases.php`.
 * Las mutaciones van a `/src/cambios/...`.
 */

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use src\actividades\application\ActividadTipo;
use web\Desplegable;
use web\DesplegableArray;
use web\Hash;
use web\TiposActividades;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $Qid_usuario = (int)strtok((string)$a_sel[0], '#');
    $Qid_item_usuario_objeto = (int)strtok('#');
} else {
    $Qid_usuario = (int)filter_input(INPUT_POST, 'id_usuario');
    $Qid_item_usuario_objeto = (int)filter_input(INPUT_POST, 'id_item_usuario_objeto');
}

$Qquien = (string)filter_input(INPUT_POST, 'quien');
$Qsalida = (string)filter_input(INPUT_POST, 'salida');

$a_campos_backend = [
    'id_usuario' => $Qid_usuario,
    'id_item_usuario_objeto' => $Qid_item_usuario_objeto,
    'salida' => $Qsalida,
];
$data = PostRequest::getDataFromUrl('/src/cambios/usuario_avisos_pref_form_data', $a_campos_backend);
$payload = is_array($data) ? $data : [];

$nombre = (string)($payload['nombre'] ?? '');
$id_item_usuario_objeto = (int)($payload['id_item_usuario_objeto'] ?? 0);
$aObjetos = (array)($payload['aObjetos'] ?? []);
$aTiposAviso = (array)($payload['aTiposAviso'] ?? []);
$aFases = (array)($payload['aFases'] ?? []);
$aOpcionesCasas = (array)($payload['aOpcionesCasas'] ?? []);
$id_pau = (string)($payload['id_pau'] ?? '');
$id_tipo_activ = (string)($payload['id_tipo_activ'] ?? '');
$id_fase_ref = (string)($payload['id_fase_ref'] ?? '');
$objeto = (string)($payload['objeto'] ?? '');
$aviso_tipo = (string)($payload['aviso_tipo'] ?? '');
$dl_propia = (bool)($payload['dl_propia'] ?? true);
$aviso_off = (bool)($payload['aviso_off'] ?? false);
$aviso_on = (bool)($payload['aviso_on'] ?? true);
$aviso_outdate = (bool)($payload['aviso_outdate'] ?? false);
$perm_jefe = (bool)($payload['perm_jefe'] ?? false);
$sfsv_text = (string)($payload['sfsv_text'] ?? '');

$oDesplTiposAviso = new Desplegable();
$oDesplTiposAviso->setNombre('aviso_tipo');
$oDesplTiposAviso->setBlanco('false');
$oDesplTiposAviso->setOpciones($aTiposAviso);
if ($aviso_tipo !== '') {
    $oDesplTiposAviso->setOpcion_sel($aviso_tipo);
}

$oDesplObjetos = new Desplegable();
$oDesplObjetos->setNombre('objeto');
$oDesplObjetos->setBlanco('true');
$oDesplObjetos->setOpciones($aObjetos);
$oDesplObjetos->setAction('fnjs_actualizar_fases(); fnjs_actualizar_propiedades()');
if ($objeto !== '') {
    $oDesplObjetos->setOpcion_sel($objeto);
}

$oDesplFases = new Desplegable();
$oDesplFases->setNombre('id_fase_ref');
$oDesplFases->setBlanco('true');
$oDesplFases->setOpciones($aFases);
if ($id_fase_ref !== '') {
    $oDesplFases->setOpcion_sel($id_fase_ref);
}

$oDesplArrayCasas = new DesplegableArray($id_pau, $aOpcionesCasas, 'casas');
$oDesplArrayCasas->setBlanco('t');
$oDesplArrayCasas->setAccionConjunto('fnjs_mas_casas(event)');

// ActividadTipo: si tenemos id_tipo_activ lo usamos, si no, construimos con piezas.
$oActividadTipo = new ActividadTipo();
$oActividadTipo->setSfsvAll(false);
if ($id_tipo_activ !== '') {
    $oActividadTipo->setId_tipo_activ($id_tipo_activ);
} else {
    $oTipoActiv = new TiposActividades();
    $oTipoActiv->setSfsvText($sfsv_text);
    $oActividadTipo->setSfsv($oTipoActiv->getSfsvText());
    $oActividadTipo->setAsistentes($oTipoActiv->getAsistentesText());
    $oActividadTipo->setActividad($oTipoActiv->getActividadText());
    $oActividadTipo->setNom_tipo($oTipoActiv->getNom_tipoText());
}
$oActividadTipo->setPara('cambios');
$oActividadTipo->setQue('buscar');
$oActividadTipo->setPerm_jefe($perm_jefe);

$oHash = new Hash();
$oHash->setCamposForm('id_fase_ref!salida!aviso_tipo!objeto!dl_propia!extendida!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val');
$oHash->setcamposNo('casas!casas_mas!casas_num!id_tipo_activ!inom_tipo_val');
$oHash->setCamposChk('aviso_off!aviso_on!aviso_outdate');
$oHash->setArraycamposHidden([
    'id_usuario' => $Qid_usuario,
    'id_item_usuario_objeto' => $id_item_usuario_objeto,
    'quien' => $Qquien,
]);

$web = rtrim(ConfigGlobal::getWeb(), '/');
$url_guardar_objeto = $web . '/src/cambios/cambio_usuario_objeto_pref_guardar';
$url_guardar_propiedades = $web . '/src/cambios/cambio_usuario_propiedad_pref_guardar_todas';
$url_preview_cond = $web . '/src/cambios/cambio_usuario_propiedad_pref_preview';
$url_get_propiedades = $web . '/frontend/cambios/controller/usuario_avisos_pref_propiedades.php';
$url_get_condicion = $web . '/frontend/cambios/controller/usuario_avisos_pref_condicion.php';
$url_get_fases = $web . '/frontend/cambios/controller/usuario_avisos_pref_fases.php';

// Hash para la llamada AJAX que actualiza el desplegable de fases.
$oHashFases = new Hash();
$oHashFases->setUrl($url_get_fases);
$oHashFases->setCamposForm('salida!dl_propia!id_tipo_activ!id_usuario!objeto');
$h_actualizar = $oHashFases->linkSinValParams();

// Hash para la llamada AJAX que pinta las propiedades del objeto.
$oHashProp = new Hash();
$oHashProp->setUrl($url_get_propiedades);
$oHashProp->setCamposForm('salida!objeto!id_item_usuario_objeto');
$h_propiedades = $oHashProp->linkSinValParams();

// Hash para la llamada AJAX que pinta el modal de condicion.
$oHashMod = new Hash();
$oHashMod->setUrl($url_get_condicion);
$oHashMod->setCamposForm('salida!objeto!propiedad!id_item');
$h_mod = $oHashMod->linkSinValParams();

$chk_propia = $dl_propia ? 'checked' : '';
$chk_otra = $dl_propia ? '' : 'checked';
$chk_off = $aviso_off ? 'checked' : '';
$chk_on = $aviso_on ? 'checked' : '';
$chk_outdate = $aviso_outdate ? 'checked' : '';

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_guardar_objeto' => $url_guardar_objeto,
    'url_guardar_propiedades' => $url_guardar_propiedades,
    'url_preview_cond' => $url_preview_cond,
    'url_get_propiedades' => $url_get_propiedades,
    'url_get_condicion' => $url_get_condicion,
    'url_get_fases' => $url_get_fases,
    'h_actualizar' => $h_actualizar,
    'h_propiedades' => $h_propiedades,
    'h_mod' => $h_mod,
    'nombre' => $nombre,
    'chk_propia' => $chk_propia,
    'chk_otra' => $chk_otra,
    'oDesplObjetos' => $oDesplObjetos,
    'id_tipo_activ' => $id_tipo_activ,
    'oActividadTipo' => $oActividadTipo,
    'oDesplFases' => $oDesplFases,
    'oDesplArrayCasas' => $oDesplArrayCasas,
    'oDesplTiposAviso' => $oDesplTiposAviso,
    'id_item_usuario_objeto' => $id_item_usuario_objeto,
    'chk_off' => $chk_off,
    'chk_on' => $chk_on,
    'chk_outdate' => $chk_outdate,
];

$oView = new ViewNewPhtml('frontend\\cambios\\controller');
$oView->renderizar('usuario_avisos_pref.phtml', $a_campos);
