<?php

use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}
$oPosicion->recordar();

// comprobar que sou un región del stgr
if (!(OrbixRuntime::miAmbito() === 'rstgr' || OrbixRuntime::miAmbito() === 'r')) {
    exit(_("Solamente lo pueden ver las regiones del stgr"));
}

$aviso = '';
$form = '';
$traslados = '';
$Qmod = (string)filter_input(INPUT_POST, 'mod');

$Qapellido1 = (string)filter_input(INPUT_POST, 'apellido1');

$a_botones = array(
    array('txt' => _("imprimir certificado"), 'click' => "fnjs_imp_certificado(this.form)"),
);

$a_cabeceras = array(
    _("alumno"),
    _("dl"),
    _("alert"),
    _("asignaturas"),
    _("id"),
);

$titulo_busqueda_por_apellidos = _("búsqueda por apellidos");
$titulo = '';

$d = PostRequest::getDataFromUrl('/src/actividadestudios/matriculas_lista_otras_r_data', [
    'apellido1' => $Qapellido1,
]);
$titulo = $d['titulo'] ?? '';
$msg_err = $d['msg_err'] ?? '';
$a_valores = $d['a_valores'] ?? [];

if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oHash = new HashFront();
$oHash->setCamposNo('sel!mod!pau!scroll_id');
$a_camposHidden = array(
    'id_dossier' => 3005,
    'permiso' => 3,
    'obj_pau' => 'Actividad',
    'queSel' => 'asig',
);
$oHash->setArraycamposHidden($a_camposHidden);


if (!empty($msg_err)) {
    echo $msg_err;
}

$oTabla = new Lista();
$oTabla->setId_tabla('mtr_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);


$oHashApellidos = new HashFront();
$oHashApellidos->setCamposForm('apellido1');
$a_camposHiddenP = [];
$oHashApellidos->setArraycamposHidden($a_camposHiddenP);

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'mod' => $Qmod,
    'oTabla' => $oTabla,
    'titulo' => $titulo,
    'titulo_busqueda_por_apellidos' => $titulo_busqueda_por_apellidos,
    'aviso' => $aviso,
    'oHashApellidos' => $oHashApellidos,
    'Qapellido1' => $Qapellido1,
];

$oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
$oView->renderizar('matriculas_otras_r.phtml', $a_campos);
