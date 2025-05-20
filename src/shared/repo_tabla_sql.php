<?php

namespace src\shared;

// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use web\Hash;
use web\Lista;
use web\Posicion;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

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

// Tiene que ser en dos pasos.
$obj = $Qclase_info;
$oInfoClase = new $obj();

// si paso parámetros, definir la colección
$Qpau = (string)filter_input(INPUT_POST, 'pau');
$Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$oInfoClase->setPau($Qpau);
$oInfoClase->setId_pau($Qid_pau);
$oInfoClase->setObj_pau($Qobj_pau);

$oDatosTabla = new DatosTablaRepo();
$oDatosTabla->setExplicacion_txt($oInfoClase->getTxtExplicacion());
$oDatosTabla->setEliminar_txt($oInfoClase->getTxtEliminar());
if (!empty($Qk_buscar)) {
    $oInfoClase->setK_buscar($Qk_buscar);
}
$oDatosTabla->setColeccion($oInfoClase->getColeccion());
$oDatosTabla->setId_sel($Qid_sel);
$oDatosTabla->setScroll_id($Qscroll_id);

$oHashBuscar = new Hash();
$oHashBuscar->setCamposForm('k_buscar');
$a_camposHiddenBuscar = array(
    'clase_info' => $Qclase_info,
    'aSerieBuscar' => $QaSerieBuscar,
    'id_pau' => $Qid_pau,
);
$oHashBuscar->setArraycamposHidden($a_camposHiddenBuscar);

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
// para el id_tabla, convierto los posibles '/' y '\' en '_' y también quito '.php'
//$oTabla->setId_tabla('datos_sql'.  $this->id_dossier);
$id_tabla = str_replace(array('/', '\\', '.php'), array('_', '_', ''), $Qclase_info);
$id_tabla = 'repo_tabla_sql_' . $id_tabla;
$oTabla->setId_tabla($id_tabla);
$oTabla->setCabeceras($oDatosTabla->getCabeceras());
$oTabla->setBotones($oDatosTabla->getBotones());
$oTabla->setDatos($oDatosTabla->getValores());

$url = ConfigGlobal::getWeb() . "/src/shared/repo_tabla_sql.php";

if (empty($Qk_buscar)) {
    $a_campos = [
        'oPosicion' => $oPosicion,
        'script' => $oDatosTabla->getScript(),
        'url' => $url,
        'oHashBuscar' => $oHashBuscar,
        'txt_buscar' => $oInfoClase->getTxtBuscar(),
        'k_buscar' => $Qk_buscar,
    ];

    if (!empty($oInfoClase->getBuscar_view())) {
        $datos_buscar = $oInfoClase->getBuscar_view();
        $namespace = $oInfoClase->getBuscar_namespace();
        $a_campos = $oInfoClase->addCampos($a_campos);
        //include(ConfigGlobal::$directorio . '/' . $datos_buscar);
        $oView = new ViewSrcPhtml($namespace);
        $oView->renderizar($datos_buscar, $a_campos);
    } else {
        $oView = new ViewSrcPhtml('src\shared\view');
        $oView->renderizar('repo_tabla_busqueda.phtml', $a_campos);
    }
}

$a_campos2 = [
    'oPosicion' => $oPosicion,
    'script' => $oDatosTabla->getScript(),
    'titulo' => $oInfoClase->getTxtTitulo(),
    'explicacion' => $oInfoClase->getTxtExplicacion(),
    'oHashSelect' => $oHashSelect,
    'oTabla' => $oTabla,
    'permiso' => $Qpermiso,
];

$oView = new ViewSrcPhtml('src\shared\view');
$oView->renderizar('repo_tabla_sql.phtml', $a_campos2);