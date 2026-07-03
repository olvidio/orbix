<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

/**
 * @return array<int|string, mixed>
 */
function tablaDB_lista_ver_campos_buscar(mixed $value): array
{
    if (!is_array($value)) {
        return [];
    }
    return $value;
}

/**
 * @param array<int|string, mixed> $variables
 * @return array<string, mixed>
 */
function tablaDB_lista_ver_view_variables(array $variables): array
{
    $out = [];
    foreach ($variables as $key => $value) {
        $out[(string) $key] = $value;
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $payload
 */
function tablaDB_lista_ver_payload_string(array $payload, string|int $key, string $default = ''): string
{
    if (!array_key_exists($key, $payload)) {
        return $default;
    }
    $value = $payload[$key];
    if (is_string($value)) {
        return $value;
    }
    if (is_int($value) || is_float($value) || is_bool($value)) {
        return (string) $value;
    }

    return $default;
}

/**
 * @return list<array<string, mixed>|string>
 */
function tablaDB_lista_ver_cabeceras(mixed $value): array
{
    if (!is_array($value)) {
        return [];
    }
    $out = [];
    foreach ($value as $item) {
        if (is_string($item) || is_array($item)) {
            $out[] = $item;
        }
    }

    return $out;
}

/**
 * @return list<array<string, mixed>>
 */
function tablaDB_lista_ver_botones(mixed $value): array
{
    if (!is_array($value)) {
        return [];
    }
    $out = [];
    foreach ($value as $item) {
        if (is_array($item)) {
            $out[] = $item;
        }
    }

    return $out;
}

/**
 * @return array<int|string, mixed>
 */
function tablaDB_lista_ver_datos(mixed $value): array
{
    return is_array($value) ? $value : [];
}

// Archivos requeridos por esta url **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// Crea los objetos de uso global **********************************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');

$a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$a_sel = is_array($a_sel_raw) ? $a_sel_raw : [];
// Si vengo de eliminar, hay que borrar el 'sel' que ha identificado el registro,
//  pues ya no existe
$Qmod = (string)filter_input(INPUT_POST, 'mod');
if ($Qmod === 'eliminar') {
    $a_sel = [];
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
ListNavSupport::bootRecordar($oPosicion, $Qrefresh);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::mergeSelectionForRecordar(ListNavSupport::buildReturnParametrosFromPost(), $Qid_sel, $Qscroll_id));


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

    $a_campos_buscar = tablaDB_lista_ver_campos_buscar($data['a_campos'] ?? null);
    $meta = tablaDB_lista_ver_campos_buscar($a_campos_buscar['documentos_form_hash_meta'] ?? null);
    if ($meta !== []) {
        $oHashDocForm = new HashFront();
        $oHashDocForm->setUrl(tablaDB_lista_ver_payload_string($meta, 'url'));
        $oHashDocForm->setCamposForm(tablaDB_lista_ver_payload_string($meta, 'campos_form'));
        $a_campos_buscar['h1'] = $oHashDocForm->linkSinValParams();
        unset($a_campos_buscar['documentos_form_hash_meta']);
    }
    $datos_buscar = tablaDB_lista_ver_payload_string($data, 'buscar_view');
    $namespace = tablaDB_lista_ver_payload_string($data, 'namespace_view', 'frontend\shared\view');

    $camposFormBuscar = 'k_buscar';
    $camposFormExtra = $a_campos_buscar['camposForm'] ?? '';
    if (is_string($camposFormExtra) && $camposFormExtra !== '') {
        $camposFormBuscar .= $camposFormExtra;
    }
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

    $viewVariables = tablaDB_lista_ver_view_variables($a_campos_buscar);
    if ($datos_buscar !== '') {
        $oView = new ViewNewPhtml($namespace);
        $oView->renderizar($datos_buscar, $viewVariables);
    } else {
        $oView = new ViewNewPhtml('frontend\shared\view');
        $oView->renderizar('tablaDB_busqueda.phtml', $viewVariables);
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
$txt_explicacion = tablaDB_lista_ver_payload_string($data, 'explicacion');
$txt_titulo = tablaDB_lista_ver_payload_string($data, 'titulo');
$script = tablaDB_lista_ver_payload_string($data, 'script');
$id_tabla = tablaDB_lista_ver_payload_string($data, 'id_tabla');
$a_cabeceras = tablaDB_lista_ver_cabeceras($data['a_cabeceras'] ?? null);
$a_botones = tablaDB_lista_ver_botones($data['a_botones'] ?? null);
$a_valores = tablaDB_lista_ver_datos($data['a_valores'] ?? null);

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