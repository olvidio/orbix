<?php
use actividades\model\entity\GestorTipoDeActividad;
use core\ConfigGlobal;
use function core\is_true;
use permisos\model\PermisosActividades;
use procesos\model\PermAccion;
use procesos\model\entity\GestorActividadFase;
use procesos\model\entity\GestorPermUsuarioActividad;
use usuarios\model\entity\GrupoOUsuario;
use web\Desplegable;
use web\TiposActividades;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************
	$oAcciones = new PermAccion();

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_usuario= (integer) strtok($a_sel[0],"#");
    $Qid_item= (string) strtok("#");
    $Qid_tipo_activ_txt= (string) strtok("#");
    $Qdl_propia= (string) strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel',$a_sel,1);
    $scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
	$Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
    $Qid_tipo_activ_txt = (integer) \filter_input(INPUT_POST, 'id_tipo_activ_txt');
    $Qdl_propia = (integer) \filter_input(INPUT_POST, 'dl_propia');
}
$Qdl_propia = is_true($Qdl_propia)? 't' : 'f';

$Qquien = (string) \filter_input(INPUT_POST, 'quien');
$Qque = (string) \filter_input(INPUT_POST, 'que');

$oUsuario = new GrupoOUsuario(array('id_usuario'=>$Qid_usuario)); // La tabla y su heredada
$nombre=$oUsuario->getUsuario();

$aAfecta_a = PermisosActividades::Afecta;
$oAcciones = new PermAccion();
$aOpcionesAction = $oAcciones->lista_array();


if (empty($Qid_tipo_activ_txt))  {// nuevo
	$Qid_tipo_activ_txt = '1.....';
	$Qdl_propia = 't';
}

$oTipoActiv= new TiposActividades($Qid_tipo_activ_txt);

$sfsv=$oTipoActiv->getSfsvText();
$asistentes=$oTipoActiv->getAsistentesText();
$actividad=$oTipoActiv->getActividadText();
$nom_tipo=$oTipoActiv->getNom_tipoText();

$id_tipo_activ = $oTipoActiv->getId_tipo_activ();
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

$GesTiposActiv = new GestorTipoDeActividad();
$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($id_tipo_activ,$Qdl_propia);

$oGesFases= new GestorActividadFase();
$oDesplFases = $oGesFases->getListaActividadFases($aTiposDeProcesos);

$aPerm = [];
$gesPermUsuarioActividad = new GestorPermUsuarioActividad();
$i = 0;
asort($aAfecta_a);
foreach ($aAfecta_a as $afecta_a_txt => $num) {
    $aWhere = [
            'id_usuario'         => $Qid_usuario,
            'dl_propia'          => $Qdl_propia,
            'id_tipo_activ_txt'  => $Qid_tipo_activ_txt,
            'afecta_a'           => $num,
        ];

    $fase_ref = '';
    $perm_on = '';
    $perm_off = '';
    $afecta_a = '';
    $cPermUsuarioActividad = $gesPermUsuarioActividad->getPermUsuarioActividades($aWhere);
    // Solo deberia haber uno???
    foreach ($cPermUsuarioActividad as $oPermiso) {
        $fase_ref = $oPermiso->getFase_ref();
        $afecta_a = $oPermiso->getAfecta_a();
        $perm_on = $oPermiso->getPerm_on();
        $perm_off = $oPermiso->getPerm_off();
    }

    $oDesplFases = $oGesFases->getListaActividadFases($aTiposDeProcesos);
    $oDesplFases->setNombre("fase_ref[]");
    $oDesplFases->setOpcion_sel($fase_ref);

    $oDesplPermOn = new Desplegable('perm_on[]',$aOpcionesAction,$perm_on,false);
    $oDesplPermOff = new Desplegable('perm_off[]',$aOpcionesAction,$perm_off,false);
    $chk = ($afecta_a == $num)? 'checked' : '';

    $aPerm[] = ['afecta_a' => $afecta_a_txt,
                'nameAfecta_a' => "afecta_a[$i]",
                'num' => $num,
                'chk' => $chk,
                'oDesplFases' => $oDesplFases,
                'oDesplPermOff' => $oDesplPermOff,
                'oDesplPermOn' => $oDesplPermOn,
                ];
    $i++; // para que cuente los indices desde 0.
}

$oHash = new web\Hash();
$oHash->setcamposForm('dl_propia!fase_ref!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val!perm_on!perm_off');
$oHash->setCamposNo('afecta_a!id_tipo_activ');
$a_camposHidden = array(
    'id_usuario' => $Qid_usuario,
    'que' => 'perm_update',
    'quien' => $Qquien,
);
$oHash->setArraycamposHidden($a_camposHidden);


$url_actualizar = core\ConfigGlobal::getWeb().'/apps/procesos/controller/usuario_perm_activ_ajax.php';
$oHash1 = new web\Hash();
$oHash1->setUrl($url_actualizar);
$oHash1->setCamposForm('dl_propia!id_tipo_activ');
$h_actualizar = $oHash1->linkSinVal();

if ( is_true($Qdl_propia) ) { 
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
    'aPerm' => $aPerm,
];

$oView = new core\ViewTwig('procesos/controller');
echo $oView->render('usuario_perm_activ.html.twig',$a_campos);
