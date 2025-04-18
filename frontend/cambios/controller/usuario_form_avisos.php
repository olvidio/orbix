<?php


// Crea los objetos de uso global **********************************************
use core\ConfigGlobal;
use core\ViewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string)filter_input(INPUT_POST, 'quien');

$oPosicion->recordar();

$url_lista_backend = Hash::link(ConfigGlobal::getWeb()
    . '/apps/cambios/controller/usuario_form_avisos.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden(['id_usuario' => $Qid_usuario, 'quien' => $Qquien]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_valores_avisos = $data['a_valores'];
$nombre_usuario = $data['nombre_usuario'];

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
$oTablaAvisos->setDatos($a_valores_avisos);


$url_usuario_ajax = ConfigGlobal::getWeb() . '/apps/usuarios/controller/usuario_ajax.php';
$oHashAvisos = new web\Hash();
$oHashAvisos->setUrl($url_usuario_ajax);
$oHashAvisos->setCamposNo('sel!scroll_id!salida');
$a_camposHidden = array(
    'id_usuario' => $Qid_usuario,
    'quien' => $Qquien,
    'salida' => '',
);
$oHashAvisos->setArraycamposHidden($a_camposHidden);
$oHashAvisos->setPrefix('av'); // prefijo par el id.
$h1 = $oHashAvisos->linkSinVal();

$a_camposAvisos = [
    'oPosicion' => $oPosicion,
    'oHashAvisos' => $oHashAvisos,
    'oTablaAvisos' => $oTablaAvisos,
    'nombre_usuario' => $nombre_usuario,
];

$oView = new ViewPhtml('../frontend/cambios/controller');
$oView->renderizar('usuario_form_avisos.phtml', $a_camposAvisos);