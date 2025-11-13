<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qclase_info = (string)filter_input(INPUT_POST, 'clase_info');
$Qdatos_buscar = (string)filter_input(INPUT_POST, 'datos_buscar');
$QaSerieBuscar = (string)filter_input(INPUT_POST, 'aSerieBuscar');
$Qk_buscar = (string)filter_input(INPUT_POST, 'k_buscar');
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qpermiso = (string)filter_input(INPUT_POST, 'permiso');
$Qid_pau = (string)filter_input(INPUT_POST, 'id_pau'); // necesario para nuevo.

$aQuery = array(
    'clase_info' => $Qclase_info,
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
    $Qs_pkey = explode('#', $a_sel[0]);
    // he cambiado las comillas dobles por simples. Deshago el cambio.
    $Qs_pkey = str_replace("'", '"', $Qs_pkey[0]);
    $a_pkey = json_decode(core\urlsafe_b64decode($Qs_pkey));
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
    $aQuery['sel'] = $a_sel;
    $aQuery['scroll_id'] = $scroll_id;
    // add stack:
    $stack = $oPosicion->getStack(1);
    $aQuery['stack'] = $stack;
} else { // si es nuevo
    $Qs_pkey = '';
    $a_pkey = '';
}

$web_depende = ConfigGlobal::getWeb() . "/src/shared/infrastructure/controllers/tablaDB_depende_datos.php";
$oHashDepende = new Hash();
$oHashDepende->setUrl($web_depende);
$oHashDepende->setCamposForm('clase_info!accion!valor_depende');
$h_depende = $oHashDepende->linkSinVal();

/* generar url go_to para volver a la tabla */
$aQuery['s_pkey'] = $Qs_pkey;
// para los dossiers
if (!empty($Qobj_pau)) {
    $aQuery['obj_pau'] = $Qobj_pau;
    $sQuery = http_build_query($aQuery);
    $Qgo_to = Hash::link(ConfigGlobal::getWeb() . "/apps/dossiers/controller/dossiers_ver.php?$sQuery");
} else {
    $sQuery = http_build_query($aQuery);
    $Qgo_to = Hash::link(ConfigGlobal::getWeb() . "/src/shared/infrastructure/controllers/tablaDB_lista_datos.php?$sQuery");
}

$url_backend = '/src/shared/infrastructure/controllers/tablaDB_formulario_datos.php';
$parametros = [
    'clase_info' => $Qclase_info,
    'a_pkey' => $a_pkey,
    'obj_pau' => $Qobj_pau,
    'mod' => $Qmod,
];
$data = PostRequest::getDataFromUrl($url_backend, $parametros);
if (!empty($data['error'])) {
    exit ($data['error']);
}

$fields = $data['fields'];
$tit_txt = $data['tit_txt'];
$explicacion_txt = $data['explicacion_txt'];
$camposForm = $data['camposForm'];
$camposNo = $data['camposNo'];

$oHashSelect = new Hash();
$oHashSelect->setCamposForm($camposForm);
$oHashSelect->setCamposNo('sel!' . $camposNo);
$a_camposHidden = array(
    'clase_info' => $Qclase_info,
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
    'clase_info' => $Qclase_info,
];

$oView = new ViewNewPhtml('frontend\shared\controller');
$oView->renderizar('tablaDB_formulario.phtml', $a_campos);