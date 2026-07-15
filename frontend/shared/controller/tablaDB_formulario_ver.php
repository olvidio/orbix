<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

// INICIO Cabecera global de URL de controlador *********************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$navState = ListNavSupport::buildReturnParametrosFromPost();
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, ListNavSupport::buildSelectionStatePatchFromPost());


$Qclase_info_encoded = (string)filter_input(INPUT_POST, 'clase_info');
$Qdatos_buscar = (string)filter_input(INPUT_POST, 'datos_buscar');
$QaSerieBuscar = (string)filter_input(INPUT_POST, 'aSerieBuscar');
$Qk_buscar = (string)filter_input(INPUT_POST, 'k_buscar');
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qpermiso = (string)filter_input(INPUT_POST, 'permiso');
$Qid_pau = (string)filter_input(INPUT_POST, 'id_pau'); // necesario para nuevo.

$aQuery = array(
    'clase_info' => $Qclase_info_encoded,
    'datos_buscar' => $Qdatos_buscar,
    'aSerieBuscar' => $QaSerieBuscar,
    "k_buscar" => $Qk_buscar,
    'id_pau' => $Qid_pau,
    'mod' => $Qmod,
    'permiso' => $Qpermiso,
);
// las claves primarias se usan para crear el objeto en el include $dir_datos.
// También se pasan por formulario al update.
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$stack = '';
if (!empty($a_sel) && ($Qmod !== 'nuevo')) { //vengo de un checkbox (para el caso de nuevo no hay que guardar el check)
    $sel0 = $a_sel[0] ?? '';
    $Qs_pkey = explode('#', is_string($sel0) ? $sel0 : '');
    // he cambiado las comillas dobles por simples. Deshago el cambio.
    $Qs_pkey = str_replace("'", '"', $Qs_pkey[0]);
    $decoded = json_decode(
        src\shared\domain\helpers\FuncTablasSupport::urlsafeB64decode($Qs_pkey),
        true
    );
    $a_pkey = is_array($decoded) ? $decoded : null;
    $aQuery['sel'] = $a_sel;
} else { // si es nuevo
    $Qs_pkey = '';
    $a_pkey = '';
}

$web_depende = AppUrlConfig::getApiBaseUrl() . "/src/shared/tablaDB_depende_datos";
$oHashDepende = new HashFront();
$oHashDepende->setUrl($web_depende);
$oHashDepende->setCamposForm('clase_info!accion!valor_depende');
$h_depende = $oHashDepende->linkSinValParams();

/* generar url go_to para volver a la tabla */
$aQuery['s_pkey'] = $Qs_pkey;
// para los dossiers
if (!empty($Qobj_pau)) {
    $aQuery['obj_pau'] = $Qobj_pau;
    $sQuery = http_build_query($aQuery);
    $Qgo_to = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . "/frontend/dossiers/controller/dossiers_ver.php?$sQuery");
} else {
    $sQuery = http_build_query($aQuery);
    $Qgo_to = HashFront::link(AppUrlConfig::getApiBaseUrl() . "/src/shared/tablaDB_lista_datos?$sQuery");
}

$url_backend = '/src/shared/tablaDB_formulario_datos';
$a_campos_backend = [
    'clase_info' => $Qclase_info_encoded,
    'a_pkey' => is_array($a_pkey) ? json_encode($a_pkey, JSON_THROW_ON_ERROR) : '',
    'obj_pau' => $Qobj_pau,
    'mod' => $Qmod,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);

$fields = $data['fields'];
$tit_txt = $data['tit_txt'];
$explicacion_txt = $data['explicacion_txt'];
$camposForm = is_string($data['camposForm'] ?? null) ? $data['camposForm'] : '';
$camposNo = is_string($data['camposNo'] ?? null) ? $data['camposNo'] : '';

$oHashSelect = new HashFront();
$oHashSelect->setCamposForm($camposForm);
$oHashSelect->setCamposNo('sel!' . $camposNo);
$a_camposHidden = array(
    'clase_info' => $Qclase_info_encoded,
    'datos_buscar' => $Qdatos_buscar,
    'aSerieBuscar' => $QaSerieBuscar,
    "k_buscar" => $Qk_buscar,
    's_pkey' => $Qs_pkey,
    'id_pau' => $Qid_pau,
    'obj_pau' => $Qobj_pau,
    'mod' => $Qmod,
    'go_to' => $Qgo_to
);
$oHashSelect->setArraycamposHidden($a_camposHidden);

$a_campos = [
    'oPosicion' => $oPosicion,
    'fields' => $fields,
    'mod' => $Qmod,
    'oHashSelect' => $oHashSelect,
    'tit_txt' => $tit_txt,
    'explicacion_txt' => $explicacion_txt,
    'web_depende' => $web_depende,
    'h_depende' => $h_depende,
    'clase_info' => $Qclase_info_encoded,
];

$oView = new ViewNewPhtml('frontend\shared\controller');
$oView->renderizar('tablaDB_formulario.phtml', $a_campos);