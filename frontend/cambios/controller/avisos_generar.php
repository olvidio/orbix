<?php
/**
 * Pantalla: listado de avisos (cambios anotados) del usuario conectado o,
 * para admins, del usuario seleccionado en el formulario superior.
 */
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\cambios\helpers\AvisosGenerarListaRender;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);
$QGstack = (int)filter_input(INPUT_POST, 'Gstack');

$is_admin = $_SESSION['oPerm']->only_perm('admin_sf') || $_SESSION['oPerm']->only_perm('admin_sv');

if ($is_admin) {
    if (!empty($Qrefresh) && !empty($QGstack)) {
        $oPosicion->goStack($QGstack);
        $Qid_usuario = (int)$oPosicion->getParametro('id_usuario');
        $Qaviso_tipo = (int)$oPosicion->getParametro('aviso_tipo');
    } else {
        $Qid_usuario = (int)filter_input(INPUT_POST, 'id_usuario');
        $Qaviso_tipo = (int)filter_input(INPUT_POST, 'aviso_tipo');
    }
} else {
    $Qid_usuario = 0;
    $Qaviso_tipo = 0;
}

$data = PostRequest::getDataFromUrl('/src/cambios/avisos_generar_lista_data', [
    'id_usuario' => $Qid_usuario,
    'aviso_tipo' => $Qaviso_tipo,
    'is_admin' => $is_admin ? 1 : 0,
]);
$data = is_array($data) ? $data : [];
$data = AvisosGenerarListaRender::enrich($data);
$Qid_usuario = (int)($data['effective_id_usuario'] ?? $Qid_usuario);
$Qaviso_tipo = (int)($data['effective_aviso_tipo'] ?? $Qaviso_tipo);
$oPosicion->setParametros([
    'id_usuario' => $Qid_usuario,
    'aviso_tipo' => $Qaviso_tipo,
], 1);
$a_valores = $data['a_valores'] ?? [];
$aOpcionesUsuarios = $data['aOpcionesUsuarios'] ?? [];
$aOpcionesAvisoTipo = $data['aOpcionesAvisoTipo'] ?? [];

$oDesplUsuarios = new Desplegable();
$oDesplUsuarios->setNombre('id_usuario');
$oDesplUsuarios->setBlanco('false');
$oDesplUsuarios->setOpciones($aOpcionesUsuarios);
if (!empty($Qid_usuario)) {
    $oDesplUsuarios->setOpcion_sel($Qid_usuario);
}

$oDesplTiposAviso = new Desplegable();
$oDesplTiposAviso->setNombre('aviso_tipo');
$oDesplTiposAviso->setBlanco('false');
$oDesplTiposAviso->setOpciones($aOpcionesAvisoTipo);
if (!empty($Qaviso_tipo)) {
    $oDesplTiposAviso->setOpcion_sel($Qaviso_tipo);
}

$stack = $oPosicion->getStack();

$oHashCond = new HashFront();
$oHashCond->setArrayCamposHidden(['Gstack' => $stack]);
$oHashCond->setCamposForm("id_usuario!aviso_tipo");

$oTabla = null;
$oHash = null;
$url_eliminar = (string)($data['url_eliminar'] ?? '');
$url_eliminar_fecha = (string)($data['url_eliminar_fecha'] ?? '');
$h_eliminar = (string)($data['h_eliminar'] ?? '');
$h_eliminar_fecha = (string)($data['h_eliminar_fecha'] ?? '');
if (!empty($Qid_usuario)) {
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
    $oTabla->setDatos($a_valores);

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
    'url_eliminar' => $url_eliminar,
    'url_eliminar_fecha' => $url_eliminar_fecha,
    'h_eliminar' => $h_eliminar,
    'h_eliminar_fecha' => $h_eliminar_fecha,
];

$oView = new ViewNewPhtml('frontend\\cambios\\view');
$oView->renderizar('avisos_generar.phtml', $a_campos_view);
