<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\inventario\helpers\InventarioPayload;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$f_ini_iso = date('Y-m-d');

$url_backend = '/src/inventario/lista_equipajes_desde_fecha';
$a_campos_backend = ['f_ini_iso' => $f_ini_iso];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = InventarioPayload::postPayload($data);
$a_opciones = InventarioPayload::desplegableOpciones($payload['a_opciones'] ?? []);

$e = 0;
$a_valores = [];
foreach ($a_opciones as $id_equipaje => $nom_equipaje) {
    $e++;
    $a_valores[$e]['sel'] = ['id' => $id_equipaje, 'select' => 'checked'];
    $a_valores[$e][1] = $nom_equipaje;
}
$a_cabeceras = [ucfirst(_('equipaje'))];

$a_botones = [['txt' => _('seleccionar'), 'click' => 'fnjs_ver_movimientos()']];

$oLista = new Lista();
$oLista->setId_tabla('tabla_equipajes');
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
$oLista->setBotones($a_botones);

$oHash = new HashFront();
$oHash->setCamposForm('sel');
$oHash->setArrayCamposHidden([
    'id_equipaje' => '',
]);

$a_campos = [
    'oHash' => $oHash,
    'oLista' => $oLista,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('equipajes_movimientos_que.phtml', $a_campos);
