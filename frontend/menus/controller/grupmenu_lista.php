<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

require_once("frontend/shared/global_header_front.inc");



$url_lista_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
    . '/src/menus/infrastructure/controllers/grupmenu_coleccion.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_valores = $data['a_valores'];

$a_cabeceras = ['grup menu', 'orden'];
$a_botones[] = ['txt' => _("modificar"),
    'click' => "fnjs_modificar(\"#seleccionados\")",
];
$a_botones[] = ['txt' => _("borrar"),
    'click' => "fnjs_eliminar(this.form)",
];



$oTabla = new Lista();
$oTabla->setId_tabla('roles_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);


$oHash = new Hash();
$oHash->setCamposForm('sel');
$oHash->setcamposNo('scroll_id');

$url_nuevo = Hash::link(ConfigGlobal::getWeb()
    . '/frontend/usuarios/controller/role_form.php?'
);

$titulo = _("listado de grupMenus");
$explicacion = '';
$action_form = '/frontend/menus/controller/grupmenu_form.php';
$action_eliminar = '';

$a_campos = [
    'oPosicion' => $oPosicion,
    'titulo' => $titulo,
    'explicacion' => $explicacion,
    'oHash' => $oHash,
    'oTabla' => $oTabla,
    'action_form' =>$action_form,
    'action_eliminar' =>$action_eliminar,
];

$oView = new ViewNewPhtml('frontend\menus\controller');
$oView->renderizar('grupmenu_lista.phtml', $a_campos);