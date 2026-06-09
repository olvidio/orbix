<?php
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/cambios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string)filter_input(INPUT_POST, 'quien');

$oPosicion->recordar();

$url_backend = '/src/cambios/usuario_form_avisos_data';
$a_campos_backend = ['id_usuario' => $Qid_usuario, 'quien' => $Qquien];
$view = cambios_usuario_form_avisos_from_payload(cambios_post_data(PostRequest::getDataFromUrl($url_backend, $a_campos_backend)));

$a_cabeceras_avisos = [
    _("objeto"),
    _("dl propia"),
    _("tipo de actividad"),
    _("fase ref."),
    _("off"),
    _("on"),
    _("outdate"),
    _("tipo de aviso"),
    _("propiedades"),
    _("valor"),
];
$a_botones_avisos = [
    array('prefix' => 'av', 'txt' => _("modificar"), 'click' => "fnjs_mod_cambio(\"#avisos\")"),
    array('prefix' => 'av', 'txt' => _("eliminar"), 'click' => "fnjs_del_cambio(\"#avisos\")")
];
$oTablaAvisos = new Lista();
$oTablaAvisos->setId_tabla('usuario_form_avisos');
$oTablaAvisos->setCabeceras($a_cabeceras_avisos);
$oTablaAvisos->setBotones($a_botones_avisos);
$oTablaAvisos->setDatos($view['a_valores']);


$url_usuario_ajax = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/cambios/controller/usuario_avisos_pref.php';
$oHashAvisos = new HashFront();
$oHashAvisos->setUrl($url_usuario_ajax);
$oHashAvisos->setCamposNo('sel!scroll_id!salida');
$a_camposHidden = array(
    'id_usuario' => $Qid_usuario,
    'quien' => $Qquien,
    'salida' => '',
);
$oHashAvisos->setArraycamposHidden($a_camposHidden);
$oHashAvisos->setPrefix('av'); // prefijo par el id.
$h1 = $oHashAvisos->linkSinValParams();

$a_camposAvisos = [
    'oPosicion' => $oPosicion,
    'oHashAvisos' => $oHashAvisos,
    'oTablaAvisos' => $oTablaAvisos,
    'nombre_usuario' => $view['nombre_usuario'],
];

$oView = new ViewNewPhtml('frontend\cambios\controller');
$oView->renderizar('usuario_form_avisos.phtml', $a_camposAvisos);
