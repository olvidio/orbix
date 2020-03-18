<?php
use actividades\model\entity\GestorTipoDeActividad;
use core\ConfigGlobal;
use procesos\model\PermAccion;
use procesos\model\PermAfectados;
use procesos\model\entity\GestorActividadFase;
use procesos\model\entity\PermUsuarioActividad;
use usuarios\model\entity\GrupoOUsuario;
use web\Desplegable;
use web\TiposActividades;
use procesos\model\CuadrosFases;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************
	$oCuadros = new PermAfectados();
	$oAcciones = new PermAccion();
	$oCuadrosFases = new CuadrosFases();

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_usuario= (integer) strtok($a_sel[0],"#");
    $Qid_item= (string) strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel',$a_sel,1);
    $scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
	$Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
    $Qid_item = (integer) \filter_input(INPUT_POST, 'id_item');
}

$Qquien = (string) \filter_input(INPUT_POST, 'quien');
$Qque = (string) \filter_input(INPUT_POST, 'que');

$oUsuario = new GrupoOUsuario(array('id_usuario'=>$Qid_usuario)); // La tabla y su heredada
$nombre=$oUsuario->getUsuario();

if (!empty($Qid_item)) {
	$oPermiso = new PermUsuarioActividad(array('id_item'=>$Qid_item, 'id_usuario'=>$Qid_usuario));
	$afecta_a=$oPermiso->getAfecta_a();
	$dl_propia=$oPermiso->getDl_propia();
	$id_tipo_activ=$oPermiso->getId_tipo_activ_txt();
	$json_fases = $oPermiso->getJsonFaseAccion();
    $oFases = json_decode($json_fases);
    if (empty($oFases)) {
        $oFases = new stdClass;
    }
	
	$GesTiposActiv = new GestorTipoDeActividad();
	$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($id_tipo_activ,$dl_propia);
	
	$oGesFases= new GestorActividadFase();
	$aFases = $oGesFases->getArrayFasesProcesos($aTiposDeProcesos);
    $oCuadrosFases->setPermissions($aFases);
    $oCuadrosFases->setoFases($oFases);
} else { // es nuevo
	$oPermiso = new PermUsuarioActividad(array('id_usuario'=>$Qid_usuario));
	$afecta_a=0;
	$dl_propia='t';
}

if (!empty($id_tipo_activ))  {
	$oTipoActiv= new TiposActividades($id_tipo_activ);
} else {
	$oTipoActiv= new TiposActividades();
}
$sfsv=$oTipoActiv->getSfsvText();
$asistentes=$oTipoActiv->getAsistentesText();
$actividad=$oTipoActiv->getActividadText();
$nom_tipo=$oTipoActiv->getNom_tipoText();

$oActividadTipo = new actividades\model\ActividadTipo();
if (!empty($id_tipo_activ))  {
    $oActividadTipo->setId_tipo_activ($id_tipo_activ);
}
$oActividadTipo->setAsistentes($asistentes);
$oActividadTipo->setActividad($actividad);
$oActividadTipo->setNom_tipo($nom_tipo);
$oActividadTipo->setPara('procesos');
$perm_jefe = FALSE;
if ($_SESSION['oConfig']->is_jefeCalendario()
    or (($_SESSION['oPerm']->have_perm_oficina('des') or $_SESSION['oPerm']->have_perm_oficina('vcsd')) && ConfigGlobal::mi_sfsv() == 1)
    or ($_SESSION['oPerm']->have_perm_oficina('calendario'))
    ) {
    $perm_jefe = TRUE;
}
$oActividadTipo->setPerm_jefe($perm_jefe);

$oHash = new web\Hash();
$oHash->setcamposForm('afecta_a!dl_propia!afases!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val');
$oHash->setcamposNo('id_tipo_activ');
$a_camposHidden = array(
    'id_usuario' => $Qid_usuario,
    'id_item' => $Qid_item,
    'que' => 'perm_update',
    'quien' => $Qquien,
);
$oHash->setArraycamposHidden($a_camposHidden);


$url_actualizar = core\ConfigGlobal::getWeb().'/apps/procesos/controller/usuario_perm_activ_ajax.php';
$oHash1 = new web\Hash();
$oHash1->setUrl($url_actualizar);
$oHash1->setCamposForm('dl_propia!id_tipo_activ');
$h_actualizar = $oHash1->linkSinVal();

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
    'nombre' => $nombre,
    'chk_propia' =>$chk_propia,
    'chk_otra' => $chk_otra,
    'oActividadTipo' => $oActividadTipo,
    'oCuadrosFases' => $oCuadrosFases,
    'oCuadros' => $oCuadros,
    'afecta_a' => $afecta_a,
];

$oView = new core\ViewTwig('procesos/controller');
echo $oView->render('usuario_perm_activ.html.twig',$a_campos);
