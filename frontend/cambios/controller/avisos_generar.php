<?php

use frontend\shared\helpers\ListNavSupport;

/**
 * Pantalla: listado de avisos (cambios anotados) del usuario conectado o,
 * para admins, del usuario seleccionado en el formulario superior.
 */

use frontend\cambios\helpers\AvisosGenerarListaRender;
use frontend\cambios\helpers\CambiosPayload;
use frontend\cambios\helpers\CambiosPermSupport;
use frontend\shared\FrontBootstrap;
use frontend\shared\PostRequest;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Desplegable;
use frontend\shared\web\Lista;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');
$QGstack = (int)filter_input(INPUT_POST, 'Gstack');

$is_admin = CambiosPermSupport::isAdmin();

if ($is_admin) {
    if (!empty($Qrefresh) && !empty($QGstack)) {
        $oPosicion->goStack($QGstack);
        $Qid_usuario = \frontend\shared\helpers\PayloadCoercion::int($oPosicion->getParametro('id_usuario'));
        $Qaviso_tipo = \frontend\shared\helpers\PayloadCoercion::int($oPosicion->getParametro('aviso_tipo'));
    } else {
        $Qid_usuario = (int)filter_input(INPUT_POST, 'id_usuario');
        $Qaviso_tipo = (int)filter_input(INPUT_POST, 'aviso_tipo');
    }
} else {
    $Qid_usuario = 0;
    $Qaviso_tipo = 0;
}

\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion, $Qrefresh);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::mergeSelectionIntoReturnParametros([
    'id_usuario' => $Qid_usuario,
    'aviso_tipo' => $Qaviso_tipo,
], \frontend\shared\helpers\ListNavSupport::idSelFromPost(), \frontend\shared\helpers\ListNavSupport::scrollIdFromPost()));

$data = AvisosGenerarListaRender::enrich(CambiosPayload::postData(PostRequest::getDataFromUrl('/src/cambios/avisos_generar_lista_data', [
    'id_usuario' => $Qid_usuario,
    'aviso_tipo' => $Qaviso_tipo,
    'is_admin' => $is_admin ? 1 : 0,
])));
$view = CambiosPayload::avisosGenerarFromPayload($data);
$Qid_usuario = $view['effective_id_usuario'];
$Qaviso_tipo = $view['effective_aviso_tipo'];
$oPosicion->setParametros([
    'id_usuario' => $Qid_usuario,
    'aviso_tipo' => $Qaviso_tipo,
], 1);

$oDesplUsuarios = new Desplegable();
$oDesplUsuarios->setNombre('id_usuario');
$oDesplUsuarios->setBlanco('false');
$oDesplUsuarios->setOpciones($view['aOpcionesUsuarios']);
if ($Qid_usuario !== 0) {
    $oDesplUsuarios->setOpcion_sel(\frontend\shared\helpers\PayloadCoercion::string($Qid_usuario));
}

$oDesplTiposAviso = new Desplegable();
$oDesplTiposAviso->setNombre('aviso_tipo');
$oDesplTiposAviso->setBlanco('false');
$oDesplTiposAviso->setOpciones($view['aOpcionesAvisoTipo']);
if ($Qaviso_tipo !== 0) {
    $oDesplTiposAviso->setOpcion_sel(\frontend\shared\helpers\PayloadCoercion::string($Qaviso_tipo));
}

$stack = $oPosicion->getStack();

$oHashCond = new HashFront();
$oHashCond->setArrayCamposHidden(['Gstack' => $stack]);
$oHashCond->setCamposForm("id_usuario!aviso_tipo");

$oTabla = null;
$oHash = null;
if ($Qid_usuario !== 0) {
    $a_cabeceras = [
        ['name' => ucfirst(_("fecha cambio")), 'class' => 'fecha_hora'],
        ucfirst(_("quien")),
        ucfirst(_("cambio")),
    ];
    $a_botones = [
        ['txt' => _("borrar"), 'click' => "fnjs_borrar(\"#seleccionados\")"],
        ['txt' => _("todos"), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"all\",0)"],
        ['txt' => _("ninguno"), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"none\",0)"],
    ];
    $oTabla = new Lista();
    $oTabla->setId_tabla('avisos_tabla');
    $oTabla->setCabeceras($a_cabeceras);
    $oTabla->setBotones($a_botones);
    $oTabla->setDatos($view['a_valores']);

    $oHash = new HashFront();
    $oHash->setArrayCamposHidden([
        'id_usuario' => $Qid_usuario,
        'aviso_tipo' => $Qaviso_tipo,
        'Gstack' => $stack,
    ]);
    $oHash->setCamposNo('f_fin!scroll_id!sel!refresh');
}

$a_campos_view = [
    'oPosicion' => $oPosicion,
    'is_admin' => $is_admin,
    'Qid_usuario' => $Qid_usuario,
    'oDesplUsuarios' => $oDesplUsuarios,
    'oDesplTiposAviso' => $oDesplTiposAviso,
    'oHashCond' => $oHashCond,
    'oTabla' => $oTabla,
    'oHash' => $oHash,
    'url_eliminar' => $view['url_eliminar'],
    'url_eliminar_fecha' => $view['url_eliminar_fecha'],
    'h_eliminar' => $view['h_eliminar'],
    'h_eliminar_fecha' => $view['h_eliminar_fecha'],
];

$oView = new ViewNewPhtml('frontend\\cambios\\view');
$oView->renderizar('avisos_generar.phtml', $a_campos_view);
