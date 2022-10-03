<?php

// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\entity\GestorEncargoTipo;
use web\Desplegable;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
//

$Qfiltro_ctr = (string)\filter_input(INPUT_POST, 'filtro_ctr');

$oGesEncargoTipo = new GestorEncargoTipo();


$opciones = $oGesEncargoTipo->getArraySeccion();
$oDesplGrupoCtrs = new Desplegable();
$oDesplGrupoCtrs->setNombre('filtro_ctr');
$oDesplGrupoCtrs->setOpciones($opciones);
$oDesplGrupoCtrs->setOpcion_sel($Qfiltro_ctr);
$oDesplGrupoCtrs->setBlanco(1);
$oDesplGrupoCtrs->setAction("fnjs_lista_propuestas();");

$url_ajax = "apps/encargossacd/controller/propuestas_ajax.php";
$oHash = new Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb() . '/' . $url_ajax);
$oHash->setCamposForm('que!filtro_ctr');
$h = $oHash->linkSinVal();

$oHash1 = new Hash();
$oHash1->setUrl(core\ConfigGlobal::getWeb() . '/' . $url_ajax);
$oHash1->setCamposForm('que!tipo!id_item!id_enc!id_sacd');
$h_cmb = $oHash1->linkSinVal();

$oHash2 = new Hash();
$oHash2->setUrl(core\ConfigGlobal::getWeb() . '/' . $url_ajax);
$oHash2->setCamposForm('que!id_sacd');
$h_info = $oHash2->linkSinVal();

$oHash3 = new Hash();
$oHash3->setUrl(core\ConfigGlobal::getWeb() . '/' . $url_ajax);
$oHash3->setCamposForm('que!id_sacd!id_item!id_enc');
$h_dedicacion = $oHash3->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    //'oHash' => $oHash,
    'h' => $h,
    'h_cmb' => $h_cmb,
    'h_info' => $h_info,
    'h_dedicacion' => $h_dedicacion,
    'url_ajax' => $url_ajax,
    'oDesplGrupoCtrs' => $oDesplGrupoCtrs,
];

$oView = new core\ViewTwig('encargossacd/controller');
echo $oView->render('propuestas_lista.html.twig', $a_campos);