<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;

// Archivos requeridos por esta url **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// Crea los objetos de uso global **********************************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// Si vengo de eliminar, hay que borrar el 'sel' que ha identificado el registro,
//  pues ya no existe
$Qmod = (string)filter_input(INPUT_POST, 'mod');
if ($Qmod === 'eliminar' && isset($a_sel)) {
    unset($a_sel);
}

//Si vengo por medio de Posicion, borro la última
$Qid_sel = (string)filter_input(INPUT_POST, 'id_sel');
$Qscroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $oPosicion2->olvidar($stack);
        }
    }
}

// en los menus esta sin codificar, pero a partir de aquí si:
$Qclase_info_encoded = (string)filter_input(INPUT_POST, 'clase_info');
if (urldecode($Qclase_info_encoded) === $Qclase_info_encoded) {
    $Qclase_info_encoded = urlencode($Qclase_info_encoded);
}

$QaSerieBuscar = filter_input(INPUT_POST, 'aSerieBuscar');
$Qk_buscar = filter_input(INPUT_POST, 'k_buscar');
if ($QaSerieBuscar !== null) {
    $QaSerieBuscar = (string)$QaSerieBuscar;
    $QaSerieBuscar = urldecode($QaSerieBuscar);
}
if ($Qk_buscar !== null) {
    $Qk_buscar = (string)$Qk_buscar;
$Qk_buscar = urldecode($Qk_buscar);
}

$Qpermiso = (integer)filter_input(INPUT_POST, 'permiso');
if (empty($Qpermiso)) {
    $Qpermiso = 3;
}

// si paso parámetros, definir la colección
$Qpau = (string)filter_input(INPUT_POST, 'pau');
$Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');


if ($QaSerieBuscar === null && $Qk_buscar === null) {
    /**********************************************************************
     ********* mostrar formulario de búsqueda
     **********************************************************************/

    // pedir a Info los datos necesarios para mostrar el formulario de búsqueda
    //$url_backend = '/src/shared/infrastructure/ui/http/controllers/tablaDB_buscar_datos.php';
    $url_backend = '/src/shared/tablaDB_buscar_datos';
    $a_campos_backend = [
        'clase_info' => $Qclase_info_encoded,
        //'k_buscar' => $Qk_buscar, // En este caso es null y no se envia
        'pau' => $Qpau,
        'id_pau' => $Qid_pau,
        'obj_pau' => $Qobj_pau,
    ];
    $data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);

    $a_campos_buscar = $data['a_campos'];
    if (!empty($a_campos_buscar['documentos_form_hash_meta']) && is_array($a_campos_buscar['documentos_form_hash_meta'])) {
        $meta = $a_campos_buscar['documentos_form_hash_meta'];
        $oHashDocForm = new HashFront();
        $oHashDocForm->setUrl((string) ($meta['url'] ?? ''));
        $oHashDocForm->setCamposForm((string) ($meta['campos_form'] ?? ''));
        $a_campos_buscar['h1'] = $oHashDocForm->linkSinValParams();
        unset($a_campos_buscar['documentos_form_hash_meta']);
    }
    $datos_buscar = empty($data['buscar_view']) ? '' : $data['buscar_view'];
    $namespace = empty($data['namespace_view']) ? 'frontend\shared\view' : $data['namespace_view'];

    $camposFormBuscar = 'k_buscar';
    $camposFormBuscar .= empty($a_campos_buscar['camposForm']) ? '' : $a_campos_buscar['camposForm'];
    $oHashBuscar = new HashFront();
    $oHashBuscar->setCamposForm($camposFormBuscar);
    $a_camposHiddenBuscar = array(
        'clase_info' => $Qclase_info_encoded,
        'aSerieBuscar' => $QaSerieBuscar,
        'id_pau' => $Qid_pau,
    );
    $oHashBuscar->setArraycamposHidden($a_camposHiddenBuscar);
    $a_campos_buscar['oHashBuscar'] = $oHashBuscar;
    $a_campos_buscar['oPosicion'] = $oPosicion;
    $a_campos_buscar['url'] = AppUrlConfig::getPublicAppBaseUrl() . "/frontend/shared/controller/tablaDB_lista_ver.php";

    if (!empty($datos_buscar)) {
        $oView = new ViewNewPhtml($namespace);
        $oView->renderizar($datos_buscar, $a_campos_buscar);
    } else {
        $oView = new ViewNewPhtml('frontend\shared\view');
        $oView->renderizar('tablaDB_busqueda.phtml', $a_campos_buscar);
    }
    exit();
}

/**********************************************************************
 ********* mostrar tabla
 **********************************************************************/

// pedir a Info los datos necesarios para mostrar la tabla
$url_backend = '/src/shared/tablaDB_lista_datos';
$a_campos_backend = [
    'clase_info' => $Qclase_info_encoded,
    'k_buscar' => $Qk_buscar,
    'pau' => $Qpau,
    'id_pau' => $Qid_pau,
    'obj_pau' => $Qobj_pau,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$txt_explicacion = $data['explicacion'];
$txt_titulo = $data['titulo'];
$script = $data['script'];
$id_tabla = $data['id_tabla'];
$a_cabeceras = $data['a_cabeceras'];
$a_botones = $data['a_botones'];
$a_valores = $data['a_valores'];

$oHashSelect = new HashFront();
$oHashSelect->setCamposForm('sel');
$oHashSelect->setCamposNo('mod!sel!scroll_id!refresh');
$a_camposHiddenSelect = array(
    'clase_info' => $Qclase_info_encoded,
    'aSerieBuscar' => $QaSerieBuscar,
    'k_buscar' => $Qk_buscar,
    'obj_pau' => $Qobj_pau,
    'id_pau' => $Qid_pau,
    'pau' => $Qpau
);
$oHashSelect->setArraycamposHidden($a_camposHiddenSelect);

$oTabla = new Lista();
$oTabla->setId_tabla($id_tabla);
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
$oTabla->setMultiSort(true);

$a_campos_lista = [
    'oPosicion' => $oPosicion,
    'script' => $script,
    'titulo' => $txt_titulo,
    'explicacion' => $txt_explicacion,
    'oHashSelect' => $oHashSelect,
    'oTabla' => $oTabla,
    'permiso' => $Qpermiso,
];

$oView = new ViewNewPhtml('frontend\shared\view');
$oView->renderizar('tablaDB_lista_ver.phtml', $a_campos_lista);