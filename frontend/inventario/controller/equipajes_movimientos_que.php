<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use src\shared\ViewSrcPhtml;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$f_ini_iso = date('Y-m-d');

$url_lista_backend = Hash::link(ConfigGlobal::getWeb()
    . '/src/inventario/controller/lista_equipajes_desde_fecha.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden(['f_ini_iso' => $f_ini_iso]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_opciones = $data['a_opciones'];

$e = 0;
$a_valores = [];
foreach ($a_opciones as $id_equipaje => $nom_equipaje) {
    $e++;
    $a_valores[$e]['sel'] = array('id' => $id_equipaje, 'select' => 'checked');
    $a_valores[$e][1] = $nom_equipaje;
    //$a_valores[$e][2]=$f_ini;
    //$a_valores[$e][3]=$f_fin;
    //$a_valores[$e][4]=$lugar;
}
$a_cabeceras[] = ucfirst(_("equipaje"));
//$a_cabeceras[]=ucfirst(_("empieza"));
//$a_cabeceras[]=ucfirst(_("termina"));
//$a_cabeceras[]=ucfirst(_("lugar"));

$a_botones[] = array('txt' => _('seleccionar'), 'click' => "fnjs_ver_movimientos()");

$oLista = new Lista();
$oLista->setId_tabla('tabla_equipajes');
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
$oLista->setBotones($a_botones);

$oHash = new Hash();
$oHash->setCamposForm('sel');
$oHash->setArrayCamposHidden([
    'id_equipaje' => '',
]);

$a_campos = [
    'oHash' => $oHash,
    'oLista' => $oLista,
];

$oView = new ViewSrcPhtml('frontend\inventario\controller');
$oView->renderizar('equipajes_movimientos_que.phtml', $a_campos);