<?php
/**
 * Controlador AJAX HTML: listado de centros con el estado de su carta
 * de presentacion (modal de seleccion de la pantalla principal).
 *
 * Delega en `/src/cartaspresentacion/ubis_lista_data` y pinta los datos
 * con `frontend\shared\web\Lista`. Sucesor de las ramas `get_dl` y `get_r` del
 * dispatcher legacy `cartas_presentacion_ajax.php`.
 */

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$campos = [
    'tipo_lista' => (string)filter_input(INPUT_POST, 'tipo_lista'),
    'poblacion_sel' => (string)filter_input(INPUT_POST, 'poblacion_sel'),
];

$data = PostRequest::getDataFromUrl('/src/cartaspresentacion/ubis_lista_data', $campos);
$payload = is_array($data) ? $data : [];

$a_cabeceras = $payload['a_cabeceras'] ?? [];
$a_valores = $payload['a_valores'] ?? [];
$explicacion = (string)($payload['explicacion'] ?? '');

$oLista = new Lista();
$oLista->setId_tabla('cartas_presentacion_ubis_lista');
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);

echo $explicacion;
echo $oLista->mostrar_tabla();
