<?php

use frontend\actividadestudios\helpers\ActividadestudiosListaSupport;
use frontend\actividadestudios\helpers\MatriculasListaPayload;
use frontend\actividadestudios\helpers\ActividadestudiosRenderSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

/** @var string|list<string> $Qid_sel */
$Qid_sel = ListNavSupport::idSelFromPost();
$Qscroll_id = ListNavSupport::scrollIdFromPost();

$navState = [];
foreach (['mod', 'apellido1', 'id_dossier', 'permiso', 'obj_pau', 'queSel', 'pau'] as $key) {
    $raw = filter_input(INPUT_POST, $key);
    if (is_scalar($raw) && (string) $raw !== '') {
        $navState[$key] = (string) $raw;
    }
}
$navState = ListNavSupport::mergeSelectionIntoReturnParametros($navState, $Qid_sel, $Qscroll_id);

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);

if (!(OrbixRuntime::miAmbito() === 'rstgr' || OrbixRuntime::miAmbito() === 'r')) {
    exit(_("Solamente lo pueden ver las regiones del stgr"));
}

$aviso = '';
$Qmod = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'mod'));
$Qapellido1 = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'apellido1'));

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

$raw = ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/matriculas_lista_otras_r_data', [
    'apellido1' => $Qapellido1,
], false));
if (!empty($raw['error'])) {
    $errorHtml = PostRequest::stripInternalCallProvenance(\frontend\shared\helpers\PayloadCoercion::string($raw['error']));
    if (str_contains($errorHtml, _('Delegaciones no dadas de alta'))
        || str_contains($errorHtml, 'Delegaciones no dadas de alta')
        || str_contains($errorHtml, _('Delegaciones sin región del stgr'))
        || str_contains($errorHtml, 'Delegaciones sin región del stgr')) {
        $aviso = $errorHtml;
    } else {
        echo $errorHtml;
        return;
    }
    $lista = MatriculasListaPayload::fromPayloadOtrasR([]);
} else {
    $lista = MatriculasListaPayload::fromPayloadOtrasR($raw);
    if ($lista['aviso'] !== '') {
        $aviso = $lista['aviso'];
    }
}

$titulo = $lista['titulo'];
$msg_err = $lista['msg_err'];
$a_valores = ActividadestudiosListaSupport::valores($lista['a_valores'], $Qid_sel, $Qscroll_id);

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
    echo \frontend\shared\helpers\PayloadCoercion::string($msg_err);
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
