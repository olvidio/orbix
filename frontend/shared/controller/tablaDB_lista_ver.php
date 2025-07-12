<?php

namespace src\shared;

// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;
use web\Posicion;

// Archivos requeridos por esta url **********************************************
require_once("frontend/shared/global_header_front.inc");
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
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
} elseif (!empty($a_sel)) { //vengo de un checkbox
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $oPosicion->addParametro('scroll_id', $Qscroll_id, 1);
}

$Qclase_info = (string)filter_input(INPUT_POST, 'clase_info');
$QaSerieBuscar = (string)filter_input(INPUT_POST, 'aSerieBuscar');
$Qk_buscar = (string)filter_input(INPUT_POST, 'k_buscar');
$Qpermiso = (integer)filter_input(INPUT_POST, 'permiso');
if (empty($Qpermiso)) {
    $Qpermiso = 3;
}

$Qclase_info = urldecode($Qclase_info);
$QaSerieBuscar = urldecode($QaSerieBuscar);
$Qk_buscar = urldecode($Qk_buscar);

// si paso parámetros, definir la colección
$Qpau = (string)filter_input(INPUT_POST, 'pau');
$Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');


if (empty($Qk_buscar)) {
    /**********************************************************************
     ********* mostrar formulario de búsqueda
     **********************************************************************/

    // pedir a Info los datos necesarios para mostrar el formulario de búsqueda
    $url_lista_buscar_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
        . '/src/shared/infrastructure/controllers/tablaDB_buscar_datos.php'
    );
    $a_campos = [
        'clase_info' => $Qclase_info,
        'k_buscar' => $Qk_buscar,
        'pau' => $Qpau,
        'id_pau' => $Qid_pau,
        'obj_pau' => $Qobj_pau,
    ];
    $oHash = new Hash();
    $oHash->setUrl($url_lista_buscar_backend);
    $oHash->setArrayCamposHidden($a_campos);
    $hash_params = $oHash->getArrayCampos();
    $data = PostRequest::getData($url_lista_buscar_backend, $hash_params);

    $a_campos_buscar = $data['a_campos'];
    $datos_buscar = empty($data['buscar_view'])? '' : $data['buscar_view'];
    $namespace = empty($data['namespace_view'])? 'frontend\shared\view' : $data['namespace_view'];

    $camposFormBuscar = 'k_buscar';
    $camposFormBuscar .= empty($a_campos_buscar['camposForm'])? '' : $a_campos_buscar['camposForm'];
    $oHashBuscar = new Hash();
    $oHashBuscar->setCamposForm($camposFormBuscar);
    $a_camposHiddenBuscar = array(
        'clase_info' => $Qclase_info,
        'aSerieBuscar' => $QaSerieBuscar,
        'id_pau' => $Qid_pau,
    );
    $oHashBuscar->setArraycamposHidden($a_camposHiddenBuscar);
    $a_campos_buscar['oHashBuscar'] = $oHashBuscar;
    $a_campos_buscar['oPosicion'] = $oPosicion;
    $a_campos_buscar['url'] = ConfigGlobal::getWeb() . "/frontend/shared/controller/tablaDB_lista_ver.php";

    if (!empty($datos_buscar)) {
        $oView = new ViewNewPhtml($namespace);
        $oView->renderizar($datos_buscar, $a_campos_buscar);
    } else {
        $oView = new ViewNewPhtml('frontend\shared\view');
        $oView->renderizar('tablaDB_busqueda.phtml', $a_campos_buscar);
    }
}

/**********************************************************************
 ********* mostrar formulario de búsqueda
 **********************************************************************/

// pedir a Info los datos necesarios para mostrar la tabla
$url_lista_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
    . '/src/shared/infrastructure/controllers/tablaDB_lista_datos.php'
);
$a_campos = [
    'clase_info' => $Qclase_info,
    'k_buscar' => $Qk_buscar,
    'pau' => $Qpau,
    'id_pau' => $Qid_pau,
    'obj_pau' => $Qobj_pau,
];
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden($a_campos);
$hash_params = $oHash->getArrayCampos();
$data = PostRequest::getData($url_lista_backend, $hash_params);

$txt_explicacion = $data['explicacion'];
$txt_titulo = $data['titulo'];
$script = $data['script'];
$id_tabla = $data['id_tabla'];
$a_cabeceras = $data['a_cabeceras'];
$a_botones = $data['a_botones'];
$a_valores = $data['a_valores'];

$oHashSelect = new Hash();
$oHashSelect->setCamposForm('sel');
$oHashSelect->setCamposNo('mod!sel!scroll_id!refresh');
$a_camposHiddenSelect = array(
    'clase_info' => $Qclase_info,
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