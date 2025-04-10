<?php

use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorTipoDeActividad;
use cambios\model\entity\CambioUsuarioObjetoPref;
use cambios\model\GestorAvisoCambios;
use core\ConfigGlobal;
use core\ViewTwig;
use web\Desplegable;
use web\DesplegableArray;
use web\Hash;
use web\TiposActividades;
use procesos\model\entity\GestorActividadFase;
use ubis\model\entity\GestorCasaDl;
use usuarios\model\entity\GrupoOUsuario;
use usuarios\model\entity\Role;
use usuarios\model\entity\Usuario;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_usuario = (integer)strtok($a_sel[0], "#");
    $Qid_item_usuario_objeto = (string)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
    $Qid_item_usuario_objeto = (integer)filter_input(INPUT_POST, 'id_item_usuario_objeto');
}

$Qquien = (string)filter_input(INPUT_POST, 'quien');
$Qsalida = (string)filter_input(INPUT_POST, 'salida');

// Si empieza por 4 es usuario, por 5 es grupo
if (substr((string)$Qid_usuario, 0, 1) === '4') {
    $oUsuario = new Usuario(array('id_usuario' => $Qid_usuario));
    $grupo = FALSE;
} else {
    $oUsuario = new GrupoOUsuario(array('id_usuario' => $Qid_usuario)); // La tabla y su heredada
    $grupo = TRUE;
}
$nombre = $oUsuario->getUsuario();

$mi_sfsv = ConfigGlobal::mi_sfsv();

// Tipos de avisos
$aTipos_aviso = CambioUsuarioObjetoPref::getTipos_aviso();

$oDesplTiposAviso = new Desplegable();
$oDesplTiposAviso->setNombre('aviso_tipo');
$oDesplTiposAviso->setBlanco('false');
$oDesplTiposAviso->setOpciones($aTipos_aviso);

// Nombre de los possibles objetos (que manejan la tablas) susceptibles de avisar.
$aObjetos = GestorAvisoCambios::getArrayObjetosPosibles();

$oDesplObjetos = new Desplegable();
$oDesplObjetos->setNombre('objeto');
$oDesplObjetos->setBlanco('true');
$oDesplObjetos->setOpciones($aObjetos);
$oDesplObjetos->setAction('fnjs_actualizar_fases(); fnjs_actualizar_propiedades()');

if ($Qsalida === 'modificar' && !empty($Qid_item_usuario_objeto)) {
    $oCambioUsuarioObjetoPref = new CambioUsuarioObjetoPref(array('id_item_usuario_objeto' => $Qid_item_usuario_objeto));
    $id_tipo_activ = $oCambioUsuarioObjetoPref->getId_tipo_activ_txt();
    $dl_org = $oCambioUsuarioObjetoPref->getDl_org();
    $objeto = $oCambioUsuarioObjetoPref->getObjeto();
    $aviso_tipo = $oCambioUsuarioObjetoPref->getAviso_tipo();
    $id_pau = $oCambioUsuarioObjetoPref->getId_pau();
    $id_fase_ref = $oCambioUsuarioObjetoPref->getId_fase_ref();
    $aviso_off = $oCambioUsuarioObjetoPref->getAviso_off();
    $aviso_on = $oCambioUsuarioObjetoPref->getAviso_on();
    $aviso_outdate = $oCambioUsuarioObjetoPref->getAviso_outdate();
    // para dl y dlf:
    $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
    $dl_propia = (ConfigGlobal::mi_dele() === $dl_org_no_f);

    $GesTiposActiv = new GestorTipoDeActividad();
    $aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($id_tipo_activ, $dl_propia);

    $oDesplObjetos->setOpcion_sel($objeto);
    $oDesplTiposAviso->setOpcion_sel($aviso_tipo);
} elseif ($Qsalida === 'nuevo') { // es nuevo
    $Qid_item_usuario_objeto = '';
    $dl_propia = true;
    $id_pau = '';

    $id_tipo_activ = $mi_sfsv . '.....';
    $GesTiposActiv = new GestorTipoDeActividad();
    $aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($id_tipo_activ, $dl_propia);
    $id_fase_ref = '';
    $aviso_off = FALSE;
    $aviso_on = TRUE;
    $aviso_outdate = FALSE;
}

if (ConfigGlobal::is_app_installed('procesos')) {
    $oGesFases = new GestorActividadFase();
    $oDesplFases = $oGesFases->getListaActividadFases($aTiposDeProcesos);
    $oDesplFases->setOpcion_sel($id_fase_ref);
} else {
    $oActividad = new ActividadAll();
    $a_status = $oActividad->getArrayStatus();
    // Quitar el status 'cualquiera'
    unset($a_status[ActividadAll::STATUS_ALL]);
    $aStatusFlip = array_flip($a_status);

    $oDesplFases = new Desplegable();
    $oDesplFases->setOpciones($aStatusFlip);
}
$oDesplFases->setNombre('id_fase_ref');

$cond = '';
switch ($mi_sfsv) {
    case 1:
        $cond = "WHERE sv = 't'";
        break;
    case 2:
        $cond = "WHERE sf = 't'";
        break;
}
// miro que rol tengo. Si soy casa, sólo veo la mía
if ($grupo === FALSE && $oUsuario->isRolePau(Role::PAU_CDC)) {
    $id_pau = $oUsuario->getId_pau();
    $sDonde = str_replace(",", " OR id_ubi=", $id_pau);
    //formulario para casas cuyo calendario de actividades interesa
    $cond = "WHERE status='t' AND (id_ubi=$sDonde)";
}
$oGCasas = new GestorCasaDl();
$oOpcionesCasas = $oGCasas->getPosiblesCasas($cond);

$oDesplArrayCasas = new DesplegableArray($id_pau, $oOpcionesCasas, 'casas');
$oDesplArrayCasas->setBlanco('t');
$oDesplArrayCasas->setAccionConjunto('fnjs_mas_casas(event)');

if (!empty($id_tipo_activ)) {
    $oTipoActiv = new TiposActividades($id_tipo_activ);
} else {
    if ($mi_sfsv === 1) $ssfsv = 'sv';
    if ($mi_sfsv === 2) $ssfsv = 'sf';
    // las casas, sf y sv
    if ($grupo === FALSE && $oUsuario->isRolePau(Role::PAU_CDC)) {
        $ssfsv = '';
    }
    $oTipoActiv = new TiposActividades();
    $oTipoActiv->setSfsvText($ssfsv);
}
$sfsv = $oTipoActiv->getSfsvText();
$asistentes = $oTipoActiv->getAsistentesText();
$actividad = $oTipoActiv->getActividadText();
$nom_tipo = $oTipoActiv->getNom_tipoText();
$id_tipo_activ = $oTipoActiv->getId_tipo_activ();

$oActividadTipo = new actividades\model\ActividadTipo();
$oActividadTipo->setSfsvAll(FALSE);
if (!empty($id_tipo_activ)) {
    $oActividadTipo->setId_tipo_activ($id_tipo_activ);
} else {
    $oActividadTipo->setSfsv($sfsv);
    $oActividadTipo->setAsistentes($asistentes);
    $oActividadTipo->setActividad($actividad);
    $oActividadTipo->setNom_tipo($nom_tipo);
}
$oActividadTipo->setPara('cambios');
$oActividadTipo->setQue('buscar');

// para las casas también: sf y sv
// y los sacd
$perm_jefe = FALSE;
if ($_SESSION['oConfig']->is_jefeCalendario()
    || (($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) && $mi_sfsv === 1)
    || ($grupo === FALSE && $oUsuario->isRolePau(Role::PAU_CDC))
    || ($grupo === FALSE && $oUsuario->isRolePau(Role::PAU_SACD))
    || ($_SESSION['oPerm']->have_perm_oficina('calendario'))
) {
    $perm_jefe = TRUE;
}
$oActividadTipo->setPerm_jefe($perm_jefe);

$oHash = new Hash();
$oHash->setCamposForm('id_fase_ref!salida!aviso_tipo!objeto!dl_propia!extendida!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val');
$oHash->setcamposNo('casas!casas_mas!casas_num!id_tipo_activ!inom_tipo_val');
$oHash->setCamposChk('aviso_off!aviso_on!aviso_outdate');
$a_camposHidden = array(
    'id_usuario' => $Qid_usuario,
    'id_item_usuario_objeto' => $Qid_item_usuario_objeto,
    'quien' => $Qquien,
);
$oHash->setArraycamposHidden($a_camposHidden);


$url_actualizar = ConfigGlobal::getWeb() . '/apps/cambios/controller/usuario_avisos_pref_ajax.php';
$oHash1 = new Hash();
$oHash1->setUrl($url_actualizar);
$oHash1->setCamposForm('salida!dl_propia!id_tipo_activ!id_usuario!objeto');
$h_actualizar = $oHash1->linkSinVal();

$oHash2 = new Hash();
$oHash2->setUrl($url_actualizar);
$oHash2->setCamposForm('salida!objeto!id_item_usuario_objeto');
$h_propiedades = $oHash2->linkSinVal();

$oHash3 = new Hash();
$oHash3->setUrl($url_actualizar);
$oHash3->setCamposForm('salida!objeto!propiedad!id_item');
$h_mod = $oHash3->linkSinVal();

if (is_true($dl_propia)) {
    $chk_propia = 'checked';
    $chk_otra = '';
} else {
    $chk_propia = '';
    $chk_otra = 'checked';
}

$chk_off = is_true($aviso_off) ? 'checked' : '';
$chk_on = is_true($aviso_on) ? 'checked' : '';
$chk_outdate = is_true($aviso_outdate) ? 'checked' : '';

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_actualizar' => $url_actualizar,
    'h_actualizar' => $h_actualizar,
    'h_propiedades' => $h_propiedades,
    'h_mod' => $h_mod,
    'nombre' => $nombre,
    'chk_propia' => $chk_propia,
    'chk_otra' => $chk_otra,
    'oDesplObjetos' => $oDesplObjetos,
    'id_tipo_activ' => $id_tipo_activ,
    'oActividadTipo' => $oActividadTipo,
    'oDesplFases' => $oDesplFases,
    'oDesplArrayCasas' => $oDesplArrayCasas,
    'oDesplTiposAviso' => $oDesplTiposAviso,
    'id_item_usuario_objeto' => $Qid_item_usuario_objeto,
    'chk_off' => $chk_off,
    'chk_on' => $chk_on,
    'chk_outdate' => $chk_outdate,
];

$oView = new ViewTwig('cambios/controller');
$oView->renderizar('usuario_avisos_pref.html.twig', $a_campos);
