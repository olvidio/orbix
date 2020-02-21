<?php
use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorTipoDeActividad;
use cambios\model\gestorAvisoCambios;
use cambios\model\entity\CambioUsuarioObjetoPref;
use core\ConfigGlobal;
use procesos\model\entity\GestorActividadFase;
use ubis\model\entity\GestorCasaDl;
use usuarios\model\entity\GrupoOUsuario;
use usuarios\model\entity\Role;
use usuarios\model\entity\Usuario;
use web\Desplegable;
use web\DesplegableArray;
use web\TiposActividades;

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************

$oPosicion->recordar();

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_usuario = (integer) strtok($a_sel[0],"#");
    $Qid_item_usuario_objeto = (string) strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel',$a_sel,1);
    $scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
    $Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
    $Qid_item_usuario_objeto = (integer) \filter_input(INPUT_POST, 'id_item_usuario_objeto');
}

$Qquien = (string) \filter_input(INPUT_POST, 'quien');

$oUsuario = new GrupoOUsuario(array('id_usuario'=>$Qid_usuario)); // La tabla y su heredada
$nombre=$oUsuario->getUsuario();
$mi_sfsv = ConfigGlobal::mi_sfsv();

// Tipos de avisos
$aTipos_aviso = CambioUsuarioObjetoPref::getTipos_aviso();

$oDesplTiposAviso = new Desplegable();
$oDesplTiposAviso->setNombre('aviso_tipo');
$oDesplTiposAviso->setBlanco('false');
$oDesplTiposAviso->setOpciones($aTipos_aviso);

// Nombre de los possibles objetos (que manejan la tablas) susceptibles de avisar.
$aObjetos = gestorAvisoCambios::getArrayObjetosPosibles();

$oDesplObjetos = new Desplegable();
$oDesplObjetos->setNombre('objeto');
$oDesplObjetos->setBlanco('true');
$oDesplObjetos->setOpciones($aObjetos);
$oDesplObjetos->setAction('fnjs_actualizar_fases(); fnjs_actualizar_propiedades()');



if (!empty($Qid_item_usuario_objeto)) {
	$oCambioUsuarioObjetoPref = new CambioUsuarioObjetoPref(array('id_item_usuario_objeto'=>$Qid_item_usuario_objeto));
	$id_tipo_activ=$oCambioUsuarioObjetoPref->getId_tipo_activ_txt();
	$id_fase_ini=$oCambioUsuarioObjetoPref->getId_fase_ini();
	$id_fase_fin=$oCambioUsuarioObjetoPref->getId_fase_fin();
	$dl_org=$oCambioUsuarioObjetoPref->getDl_org();
	$objeto=$oCambioUsuarioObjetoPref->getObjeto();
	$aviso_tipo=$oCambioUsuarioObjetoPref->getAviso_tipo();
	$id_pau=$oCambioUsuarioObjetoPref->getId_pau();
	
	// para dl y dlf:
	$dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
	$dl_propia = (ConfigGlobal::mi_dele() == $dl_org_no_f)? 't' : 'f';
	
	$GesTiposActiv = new GestorTipoDeActividad();
	$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($id_tipo_activ,$dl_propia);

	if (ConfigGlobal::is_app_installed('procesos')) {
        $oGesFases= new GestorActividadFase();
        $oDesplFasesIni = $oGesFases->getListaActividadFases($aTiposDeProcesos);
        $oDesplFasesIni->setNombre('fase_ini');
        $oDesplFasesIni->setOpcion_sel($id_fase_ini);
        $oGesFases2= new GestorActividadFase();
        $oDesplFasesFin = $oGesFases2->getListaActividadFases($aTiposDeProcesos);
        $oDesplFasesFin->setNombre('fase_fin');
        $oDesplFasesFin->setOpcion_sel($id_fase_fin);
	} else {
	    $oActividad = new ActividadAll();
	    $a_status = $oActividad->getArrayStatus();
        $oDesplFasesIni = new Desplegable();
        $oDesplFasesIni->setOpciones($a_status);
        $oDesplFasesIni->setNombre('fase_ini');
        $oDesplFasesIni->setOpcion_sel($id_fase_ini);
        $oDesplFasesFin = new Desplegable();
        $oDesplFasesFin->setOpciones($a_status);
        $oDesplFasesFin->setNombre('fase_fin');
        $oDesplFasesFin->setOpcion_sel($id_fase_fin);
	}

	$oDesplObjetos->setOpcion_sel($objeto);
	$oDesplTiposAviso->setOpcion_sel($aviso_tipo);
} else { // es nuevo
	$dl_propia='t';
	$id_pau='';
	$oDesplFasesIni = new Desplegable();
	$oDesplFasesIni->setOpciones([]);
	$oDesplFasesFin = new Desplegable();
	$oDesplFasesFin->setOpciones([]);
}

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
$oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
if ($oMiUsuario->isRolePau(Role::PAU_CDC)) {
    $id_pau=$oMiUsuario->getId_pau();
    $sDonde=str_replace(",", " OR id_ubi=", $id_pau);
    //formulario para casas cuyo calendario de actividades interesa
    $cond = "WHERE status='t' AND (id_ubi=$sDonde)";
}
$oGCasas = new GestorCasaDl();
$oOpcionesCasas = $oGCasas->getPosiblesCasas($cond);

$oDesplArrayCasas = new DesplegableArray($id_pau,$oOpcionesCasas,'casas');
$oDesplArrayCasas->setBlanco('t');
$oDesplArrayCasas->setAccionConjunto('fnjs_mas_casas(event)');

if (!empty($id_tipo_activ))  {
    $oTipoActiv= new TiposActividades($id_tipo_activ);
} else {
    if ($mi_sfsv == 1) $ssfsv = 'sv';
    if ($mi_sfsv == 2) $ssfsv = 'sf';
    // las casas, sf y sv
    if ($oMiUsuario->isRolePau(Role::PAU_CDC)) {
        $ssfsv = '';
    }
    $oTipoActiv= new TiposActividades();
    $oTipoActiv->setSfsvText($ssfsv);
}
$sfsv=$oTipoActiv->getSfsvText();
$asistentes=$oTipoActiv->getAsistentesText();
$actividad=$oTipoActiv->getActividadText();
$nom_tipo=$oTipoActiv->getNom_tipoText();
$id_tipo_activ = $oTipoActiv->getId_tipo_activ();

$oActividadTipo = new actividades\model\ActividadTipo();
if (!empty($id_tipo_activ))  {
    $oActividadTipo->setId_tipo_activ($id_tipo_activ);
} else {
    $oActividadTipo->setSfsv($sfsv);
    $oActividadTipo->setAsistentes($asistentes);
    $oActividadTipo->setActividad($actividad);
    $oActividadTipo->setNom_tipo($nom_tipo);
}
$oActividadTipo->setPara('cambios');
$oActividadTipo->setQue('buscar');

// para admin y las casas también: sf y sv
$perm_jefe = FALSE;
if ($_SESSION['oConfig']->is_jefeCalendario()
    OR (($_SESSION['oPerm']->have_perm_oficina('des') or $_SESSION['oPerm']->have_perm_oficina('vcsd')) && $mi_sfsv == 1) 
    OR $oMiUsuario->isRolePau(Role::PAU_CDC)
    OR $_SESSION['oPerm']->have_perm_oficina('admin') 
    )
{
    $perm_jefe = TRUE;
}
$oActividadTipo->setPerm_jefe($perm_jefe);

$oHash = new web\Hash();
$oHash->setcamposForm('salida!aviso_tipo!objeto!dl_propia!fase_ini!fase_fin!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val');
$oHash->setcamposNo('casas_mas!casas_num!id_tipo_activ!inom_tipo_val');
$a_camposHidden = array(
    'id_usuario' => $Qid_usuario,
    'id_item_usuario_objeto' => $Qid_item_usuario_objeto,
    'quien' => $Qquien,
);
$oHash->setArraycamposHidden($a_camposHidden);


$url_actualizar = ConfigGlobal::getWeb().'/apps/cambios/controller/usuario_avisos_pref_ajax.php';
$oHash1 = new web\Hash();
$oHash1->setUrl($url_actualizar);
$oHash1->setCamposForm('salida!dl_propia!id_tipo_activ!id_usuario!objeto');
$h_actualizar = $oHash1->linkSinVal();

$oHash2 = new web\Hash();
$oHash2->setUrl($url_actualizar);
$oHash2->setCamposForm('salida!objeto!id_item_usuario_objeto');
$h_propiedades = $oHash2->linkSinVal();

$oHash3 = new web\Hash();
$oHash3->setUrl($url_actualizar);
$oHash3->setCamposForm('salida!objeto!propiedad!id_item');
$h_mod = $oHash3->linkSinVal();


if ($dl_propia == 't') {
    $chk_propia='checked';
    $chk_otra='';
} else {
    $chk_propia='';
    $chk_otra='checked';
}

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_actualizar' => $url_actualizar,
    'h_actualizar' => $h_actualizar,
    'h_propiedades' => $h_propiedades,
    'h_mod' => $h_mod,
    'nombre' => $nombre,
    'chk_propia' =>$chk_propia,
    'chk_otra' => $chk_otra,
    'oDesplObjetos' => $oDesplObjetos,
    'id_tipo_activ' => $id_tipo_activ,
    'oActividadTipo' => $oActividadTipo,
    'oDesplFasesIni' => $oDesplFasesIni,
    'oDesplFasesFin' => $oDesplFasesFin,
    'oDesplArrayCasas' => $oDesplArrayCasas,
    'oDesplTiposAviso' => $oDesplTiposAviso,
    'id_item_usuario_objeto' => $Qid_item_usuario_objeto,
];

$oView = new core\ViewTwig('cambios/controller');
echo $oView->render('usuario_avisos_pref.html.twig',$a_campos);
