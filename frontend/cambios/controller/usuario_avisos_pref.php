<?php

use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\PayloadCoercion;

/**
 * Pantalla: configuracion de avisos por usuario/grupo.
 */
use frontend\cambios\helpers\CambiosPayload;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\web\DesplegableArray;
use frontend\cambios\helpers\UsuarioAvisosPrefFormRender;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $Qid_usuario = (int)strtok((string)$a_sel[0], '#');
    $Qid_item_usuario_objeto = (int)strtok('#');
} else {
    $Qid_usuario = (int)filter_input(INPUT_POST, 'id_usuario');
    $Qid_item_usuario_objeto = (int)filter_input(INPUT_POST, 'id_item_usuario_objeto');
}

$navIdentity = $Qid_item_usuario_objeto > 0 ? ['id_item_usuario_objeto' => $Qid_item_usuario_objeto] : [];
$navState = ListNavSupport::buildReturnParametrosFromPost();
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $navIdentity,
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, ListNavSupport::buildSelectionStatePatchFromPost());

$Qquien = (string)filter_input(INPUT_POST, 'quien');
$Qsalida = (string)filter_input(INPUT_POST, 'salida');

$payload = UsuarioAvisosPrefFormRender::enrich(CambiosPayload::postData(PostRequest::getDataFromUrl('/src/cambios/usuario_avisos_pref_form_data', [
    'id_usuario' => $Qid_usuario,
    'id_item_usuario_objeto' => $Qid_item_usuario_objeto,
    'salida' => $Qsalida,
    'quien' => $Qquien,
])));
$form = CambiosPayload::usuarioAvisosPrefFormFromPayload($payload);

$oDesplTiposAviso = new Desplegable();
$oDesplTiposAviso->setNombre('aviso_tipo');
$oDesplTiposAviso->setBlanco('false');
$oDesplTiposAviso->setOpciones($form['aTiposAviso']);
if ($form['aviso_tipo'] !== '') {
    $oDesplTiposAviso->setOpcion_sel($form['aviso_tipo']);
}

$oDesplObjetos = new Desplegable();
$oDesplObjetos->setNombre('objeto');
$oDesplObjetos->setBlanco('true');
$oDesplObjetos->setOpciones($form['aObjetos']);
$oDesplObjetos->setAction('fnjs_actualizar_fases(); fnjs_actualizar_propiedades()');
if ($form['objeto'] !== '') {
    $oDesplObjetos->setOpcion_sel($form['objeto']);
}

$oDesplFases = new Desplegable();
$oDesplFases->setNombre('id_fase_ref');
$oDesplFases->setBlanco('true');
$oDesplFases->setOpciones($form['aFases']);
if ($form['id_fase_ref'] !== '') {
    $oDesplFases->setOpcion_sel($form['id_fase_ref']);
}

$oDesplArrayCasas = new DesplegableArray($form['id_pau'], $form['aOpcionesCasas'], 'casas');
$oDesplArrayCasas->setBlanco('t');
$oDesplArrayCasas->setAccionConjunto('fnjs_mas_casas(event)');

$chk_propia = $form['dl_propia'] ? 'checked' : '';
$chk_otra = $form['dl_propia'] ? '' : 'checked';
$chk_off = $form['aviso_off'] ? 'checked' : '';
$chk_on = $form['aviso_on'] ? 'checked' : '';
$chk_outdate = $form['aviso_outdate'] ? 'checked' : '';

$a_campos = array_merge($payload, [
    'oPosicion' => $oPosicion,
    'oDesplObjetos' => $oDesplObjetos,
    'oDesplFases' => $oDesplFases,
    'oDesplArrayCasas' => $oDesplArrayCasas,
    'oDesplTiposAviso' => $oDesplTiposAviso,
    'chk_propia' => $chk_propia,
    'chk_otra' => $chk_otra,
    'chk_off' => $chk_off,
    'chk_on' => $chk_on,
    'chk_outdate' => $chk_outdate,
    'id_tipo_activ' => $form['id_tipo_activ'],
]);

$oView = new ViewNewPhtml('frontend\\cambios\\view');
$oView->renderizar('usuario_avisos_pref.phtml', $a_campos);
