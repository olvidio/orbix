<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\inventario\helpers\InventarioPayload;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_cdc = (int)filter_input(INPUT_POST, 'id_cdc');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qinicio = (string)filter_input(INPUT_POST, 'inicio');
$Qfin = (string)filter_input(INPUT_POST, 'fin');

$url_backend = '/src/inventario/equipajes_lista_activ_periodo';
$a_campos_backend = [
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'incio' => $Qinicio,
    'fin' => $Qfin,
    'id_cdc' => $Qid_cdc,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = InventarioPayload::postPayload($data);
$a_valores = ActividadesListaSupport::datos($payload['a_valores'] ?? []);
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


$oHash = new HashFront();
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

AjaxJsonSupport::renderPhtml('frontend\inventario\controller', 'equipajes_lista_activ_periodo.phtml', $a_campos);
