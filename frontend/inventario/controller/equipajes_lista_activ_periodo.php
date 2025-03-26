<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_cdc = (int)filter_input(INPUT_POST, 'id_cdc');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qinicio = (string)filter_input(INPUT_POST, 'inicio');
$Qfin = (string)filter_input(INPUT_POST, 'fin');

$url_lista_backend = Hash::link(ConfigGlobal::getWeb()
    . '/apps/inventario/controller/equipajes_lista_activ_periodo.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$aCamposHidden = [
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'incio' => $Qinicio,
    'fin' => $Qfin,
    'id_cdc' => $Qid_cdc,
];
$oHash->setArrayCamposHidden($aCamposHidden);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_valores = $data['a_valores'];
$nombre_ubi = $data['nombre_ubi'];

$a_cabeceras[] = ucfirst(_("empieza"));
$a_cabeceras[] = ucfirst(_("termina"));
$a_cabeceras[] = ucfirst(_("actividad"));
$a_cabeceras[] = ucfirst(_("observaciones"));

$a_botones[] = array('txt' => _('seleccionar'), 'click' => "fnjs_nombrar_equipaje()");

$oLista = new Lista();
$oLista->setId_tabla('doc_activ_tabla');
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
$oLista->setBotones($a_botones);


$oHash = new Hash();
$oHash->setCamposForm('sel');
$oHash->setArrayCamposHidden([
    'id_cdc' => $Qid_cdc,
    'nom_equip' => '',
]);

$a_campos = [
    'nombre_ubi' => $nombre_ubi,
    'oHash' => $oHash,
    'oLista' => $oLista,
];

$oView = new ViewPhtml('../frontend/inventario/controller');
$oView->renderizar('equipajes_lista_activ_periodo.phtml', $a_campos);
