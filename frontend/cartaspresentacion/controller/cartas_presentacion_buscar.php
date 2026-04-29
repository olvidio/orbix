<?php
/**
 * Pantalla frontend: formulario de busqueda de cartas de presentacion.
 *
 * Sucesor de `apps/cartaspresentacion/controller/cartas_presentacion_buscar.php`.
 * Los datos (opciones, rutas y parametros de hash) se obtienen del
 * endpoint `/src/cartaspresentacion/cartas_presentacion_buscar_data`;
 * {@see \frontend\cartaspresentacion\helpers\CartasPresentacionBuscarOpcionesRender}
 * compone `url_lista` y `hash_lista_html`.
 * los `<select>` se montan en la vista con `frontend\shared\web\Desplegable`.
 *
 * El form postea a `frontend/cartaspresentacion/controller/cartas_presentacion_lista.php`
 * con `que=get` y `poblacion/region/pais/dl` como filtros.
 */

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\cartaspresentacion\helpers\CartasPresentacionBuscarOpcionesRender;

require_once 'frontend/shared/global_header_front.inc';

$data = PostRequest::getDataFromUrl('/src/cartaspresentacion/cartas_presentacion_buscar_data');
$payload = is_array($data) ? $data : [];
$payload = CartasPresentacionBuscarOpcionesRender::enrich($payload);

$opciones_region = (array)($payload['opciones_region'] ?? []);
$opciones_pais = (array)($payload['opciones_pais'] ?? []);
$opciones_delegacion = (array)($payload['opciones_delegacion'] ?? []);

$oDesplRegion = Desplegable::desdeOpciones($opciones_region, 'region');
$oDesplPais = new Desplegable();
$oDesplPais->setOpciones($opciones_pais);
$oDesplPais->setNombre('pais');
$oDesplDelegacion = Desplegable::desdeOpciones($opciones_delegacion, 'dl');

$url_lista = (string)($payload['url_lista'] ?? '');
$hash_lista_html = (string)($payload['hash_lista_html'] ?? '');

$a_campos = [
    'oPosicion' => $oPosicion,
    'hash_lista_html' => $hash_lista_html,
    'url_lista' => $url_lista,
    'oDesplRegion' => $oDesplRegion,
    'oDesplPais' => $oDesplPais,
    'oDesplDelegacion' => $oDesplDelegacion,
];

$oView = new ViewNewPhtml('frontend\\cartaspresentacion\\view');
$oView->renderizar('cartas_presentacion_buscar.phtml', $a_campos);
