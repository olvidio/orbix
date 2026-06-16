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

require_once __DIR__ . '/../helpers/cambios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');
$QGstack = (int)filter_input(INPUT_POST, 'Gstack');

$is_admin = cambios_is_admin();

if ($is_admin) {
    if (!empty($Qrefresh) && !empty($QGstack)) {
        $oPosicion->goStack($QGstack);
        $Qid_usuario = tessera_imprimir_int($oPosicion->getParametro('id_usuario'));
        $Qaviso_tipo = tessera_imprimir_int($oPosicion->getParametro('aviso_tipo'));
    } else {
        $Qid_usuario = (int)filter_input(INPUT_POST, 'id_usuario');
        $Qaviso_tipo = (int)filter_input(INPUT_POST, 'aviso_tipo');
    }
} else {
    $Qid_usuario = 0;
    $Qaviso_tipo = 0;
}

list_nav_boot_recordar($oPosicion, $Qrefresh);
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros([
    'id_usuario' => $Qid_usuario,
    'aviso_tipo' => $Qaviso_tipo,
], list_nav_id_sel_from_post(), list_nav_scroll_id_from_post()));

$data = AvisosGenerarListaRender::enrich(cambios_post_data(PostRequest::getDataFromUrl('/src/cambios/avisos_generar_lista_data', [
    'id_usuario' => $Qid_usuario,
    'aviso_tipo' => $Qaviso_tipo,
    'is_admin' => $is_admin ? 1 : 0,
])));
$view = cambios_avisos_generar_from_payload($data);
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
    $oDesplUsuarios->setOpcion_sel(tessera_imprimir_string($Qid_usuario));
}

$oDesplTiposAviso = new Desplegable();
$oDesplTiposAviso->setNombre('aviso_tipo');
$oDesplTiposAviso->setBlanco('false');
$oDesplTiposAviso->setOpciones($view['aOpcionesAvisoTipo']);
if ($Qaviso_tipo !== 0) {
    $oDesplTiposAviso->setOpcion_sel(tessera_imprimir_string($Qaviso_tipo));
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
