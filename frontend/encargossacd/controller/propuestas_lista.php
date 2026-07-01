<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

use frontend\shared\FrontBootstrap;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\security\HashFront;
use frontend\shared\web\Desplegable;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\shared\infrastructure\DependencyResolver;

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qfiltro_ctr = encargossacd_post_string('filtro_ctr');

/** @var EncargoAplicacionService $oService */
$oService = DependencyResolver::get(EncargoAplicacionService::class);
$opciones = $oService->getArraySeccion();

$oDesplGrupoCtrs = new Desplegable();
$oDesplGrupoCtrs->setNombre('filtro_ctr');
$oDesplGrupoCtrs->setOpciones($opciones);
$oDesplGrupoCtrs->setOpcion_sel($Qfiltro_ctr);
$oDesplGrupoCtrs->setBlanco(1);
$oDesplGrupoCtrs->setAction('fnjs_lista_propuestas();');

$url_ajax = 'frontend/encargossacd/controller/propuestas_ajax.php';

$oHash = new HashFront();
$oHash->setUrl($url_ajax);
$oHash->setCamposForm('que!filtro_ctr');
$h = $oHash->linkSinValParams();

$oHash1 = new HashFront();
$oHash1->setUrl($url_ajax);
$oHash1->setCamposForm('que!tipo!id_item!id_enc!id_sacd');
$h_cmb = $oHash1->linkSinValParams();

$oHash2 = new HashFront();
$oHash2->setUrl($url_ajax);
$oHash2->setCamposForm('que!id_sacd');
$h_info = $oHash2->linkSinValParams();

$oHash3 = new HashFront();
$oHash3->setUrl($url_ajax);
$oHash3->setCamposForm('que!id_sacd!id_item!id_enc');
$h_dedicacion = $oHash3->linkSinValParams();

$a_campos = [
    'oPosicion' => $oPosicion,
    'h' => $h,
    'h_cmb' => $h_cmb,
    'h_info' => $h_info,
    'h_dedicacion' => $h_dedicacion,
    'url_ajax' => $url_ajax,
    'oDesplGrupoCtrs' => $oDesplGrupoCtrs,
];

$oView = new ViewNewTwig('frontend/encargossacd/controller');
$oView->renderizar('propuestas_lista.html.twig', $a_campos);
