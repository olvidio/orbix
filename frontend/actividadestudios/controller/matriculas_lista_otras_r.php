<?php

use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/actividadestudios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();

$Qid_sel = null;
$Qscroll_id = null;
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}
list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_for_recordar(list_nav_build_return_parametros_from_post(), $Qid_sel, $Qscroll_id));


if (!(OrbixRuntime::miAmbito() === 'rstgr' || OrbixRuntime::miAmbito() === 'r')) {
    exit(_("Solamente lo pueden ver las regiones del stgr"));
}

$aviso = '';
$Qmod = tessera_imprimir_string(filter_input(INPUT_POST, 'mod'));
$Qapellido1 = tessera_imprimir_string(filter_input(INPUT_POST, 'apellido1'));

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

$raw = actividadestudios_post_data(PostRequest::getDataFromUrl('/src/actividadestudios/matriculas_lista_otras_r_data', [
    'apellido1' => $Qapellido1,
], false));
if (!empty($raw['error'])) {
    $errorHtml = PostRequest::stripInternalCallProvenance(tessera_imprimir_string($raw['error']));
    if (str_contains($errorHtml, _('Delegaciones no dadas de alta'))
        || str_contains($errorHtml, 'Delegaciones no dadas de alta')
        || str_contains($errorHtml, _('Delegaciones sin región del stgr'))
        || str_contains($errorHtml, 'Delegaciones sin región del stgr')) {
        $aviso = $errorHtml;
    } else {
        echo $errorHtml;
        return;
    }
    $lista = actividadestudios_matriculas_lista_otras_r_from_payload([]);
} else {
    $lista = actividadestudios_matriculas_lista_otras_r_from_payload($raw);
    if ($lista['aviso'] !== '') {
        $aviso = $lista['aviso'];
    }
}

$titulo = $lista['titulo'];
$msg_err = $lista['msg_err'];
$a_valores = actividadestudios_lista_valores($lista['a_valores'], $Qid_sel, $Qscroll_id);

$oHash = new HashFront();
$oHash->setCamposNo('sel!mod!pau!scroll_id!id_sel!id_pau');
$a_camposHidden = array(
    'id_dossier' => 3005,
    'permiso' => 3,
    'obj_pau' => 'Actividad',
    'queSel' => 'asig',
);
$oHash->setArraycamposHidden($a_camposHidden);

if ($msg_err !== '') {
    actividadestudios_echo_string($msg_err);
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
