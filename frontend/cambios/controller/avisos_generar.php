<?php
/**
 * Pantalla: listado de avisos (cambios anotados) del usuario conectado o,
 * para admins, del usuario seleccionado en el formulario superior.
 *
 * Migrada desde `apps/cambios/controller/avisos_generar.php` +
 * `avisos_generar_ajax.php` siguiendo `refactor.md`. Los endpoints backend
 * viven en `/src/cambios/...`. La eliminacion por seleccion o por fecha
 * son mutaciones JSON.
 */

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use src\cambios\domain\value_objects\AvisoTipoId;
use web\Desplegable;
use web\Hash;
use web\Lista;

require_once("frontend/shared/global_header_front.inc");

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
    $Qid_usuario = (int)ConfigGlobal::mi_id_usuario();
    $Qaviso_tipo = AvisoTipoId::TIPO_LISTA;
}

$oPosicion->setParametros([
    'id_usuario' => $Qid_usuario,
    'aviso_tipo' => $Qaviso_tipo,
], 1);

$a_campos_backend = [
    'id_usuario' => $Qid_usuario,
    'aviso_tipo' => $Qaviso_tipo,
];
$data = PostRequest::getDataFromUrl('/src/cambios/avisos_generar_lista_data', $a_campos_backend);
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

$oHashCond = new Hash();
$oHashCond->setArrayCamposHidden(['Gstack' => $stack]);
$oHashCond->setCamposForm("id_usuario!aviso_tipo");

$oTabla = null;
$oHash = null;
$url_eliminar = '';
$url_eliminar_fecha = '';
$h_eliminar = '';
$h_eliminar_fecha = '';
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

    $oHash = new Hash();
    $oHash->setArrayCamposHidden([
        'id_usuario' => $Qid_usuario,
        'aviso_tipo' => $Qaviso_tipo,
        'Gstack' => $stack,
    ]);
    $oHash->setCamposNo('f_fin!scroll_id!sel!refresh');

    // Hashes para las mutaciones (AJAX JSON).
    $web = rtrim(ConfigGlobal::getWeb(), '/');
    $url_eliminar = $web . '/src/cambios/cambio_usuario_eliminar';
    $url_eliminar_fecha = $web . '/src/cambios/cambio_usuario_eliminar_hasta_fecha';

    $oHashElim = new Hash();
    $oHashElim->setUrl($url_eliminar);
    $oHashElim->setCamposNo('sel');
    $h_eliminar = $oHashElim->linkSinValParams();

    $oHashElimF = new Hash();
    $oHashElimF->setUrl($url_eliminar_fecha);
    $oHashElimF->setCamposForm('f_fin');
    $h_eliminar_fecha = $oHashElimF->linkSinValParams();
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

$oView = new ViewNewPhtml('frontend\\cambios\\controller');
$oView->renderizar('avisos_generar.phtml', $a_campos_view);
