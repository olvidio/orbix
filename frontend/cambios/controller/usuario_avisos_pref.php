<?php
/**
 * Pantalla: configuracion de avisos por usuario/grupo.
 */
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\web\DesplegableArray;
use frontend\cambios\helpers\UsuarioAvisosPrefFormRender;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
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

$payload = PostRequest::getDataFromUrl('/src/cambios/usuario_avisos_pref_form_data', [
    'id_usuario' => $Qid_usuario,
    'id_item_usuario_objeto' => $Qid_item_usuario_objeto,
    'salida' => $Qsalida,
    'quien' => $Qquien,
]);
if (!is_array($payload)) {
    $payload = [];
}
$payload = UsuarioAvisosPrefFormRender::enrich($payload);

$aTiposAviso = (array)($payload['aTiposAviso'] ?? []);
$aObjetos = (array)($payload['aObjetos'] ?? []);
$aFases = (array)($payload['aFases'] ?? []);
$aOpcionesCasas = (array)($payload['aOpcionesCasas'] ?? []);
$id_pau = (string)($payload['id_pau'] ?? '');
$aviso_tipo = (string)($payload['aviso_tipo'] ?? '');
$objeto = (string)($payload['objeto'] ?? '');
$id_fase_ref = (string)($payload['id_fase_ref'] ?? '');
$dl_propia = (bool)($payload['dl_propia'] ?? true);
$aviso_off = (bool)($payload['aviso_off'] ?? false);
$aviso_on = (bool)($payload['aviso_on'] ?? true);
$aviso_outdate = (bool)($payload['aviso_outdate'] ?? false);
$id_tipo_activ = (string)($payload['id_tipo_activ'] ?? '');

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

$chk_propia = $dl_propia ? 'checked' : '';
$chk_otra = $dl_propia ? '' : 'checked';
$chk_off = $aviso_off ? 'checked' : '';
$chk_on = $aviso_on ? 'checked' : '';
$chk_outdate = $aviso_outdate ? 'checked' : '';

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
    'id_tipo_activ' => $id_tipo_activ,
]);

$oView = new ViewNewPhtml('frontend\\cambios\\view');
$oView->renderizar('usuario_avisos_pref.phtml', $a_campos);
