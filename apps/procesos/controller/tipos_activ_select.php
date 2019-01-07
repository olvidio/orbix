<?php
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
	
$ssfsv = '';
$sasistentes='';
$sactividad='';
$snom_tipo='';
$id_tipo_activ = '';

$url_ajax = "apps/procesos/controller/tipos_activ_ajax.php";

$oHashSave = new Hash();
$oHashSave->setUrl($url_ajax);
$oHashSave->setcamposForm('que!id_tipo_activ!dl_propia!nombre!id_tipo_proceso');
$h_guardar = $oHashSave->linkSinVal();

$oHashVer = new web\Hash();
$oHashVer->setUrl($url_ajax);
$oHashVer->setCamposForm('que!id_tipo_activ!dl_propia');
$h_ver = $oHashVer->linkSinVal();

$oHashLista = new web\Hash();
$oHashLista->setUrl($url_ajax);
$oHashLista->setCamposForm('que');
$h_lista = $oHashLista->linkSinVal();

$oActividadTipo = new actividades\model\ActividadTipo();
$oActividadTipo->setId_tipo_activ($id_tipo_activ);
$oActividadTipo->setAsistentes($sasistentes);
$oActividadTipo->setActividad($sactividad);
$oActividadTipo->setNom_tipo($snom_tipo);

$txt_eliminar = _("¿Esta seguro que desea borrar esta fase?");

$a_campos = ['oPosicion' => $oPosicion,
    'h_guardar' => $h_guardar,
    'h_ver' => $h_ver,
    'h_lista' => $h_lista,
    'oActividadTipo' => $oActividadTipo,
    'url_ajax' => $url_ajax,
    'txt_eliminar' => $txt_eliminar,
];

$oView = new core\ViewTwig('procesos/controller');
echo $oView->render('tipos_activ_select.html.twig',$a_campos);