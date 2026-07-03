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

use frontend\cartaspresentacion\helpers\CartaspresentacionPayload;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\cartaspresentacion\helpers\CartasPresentacionBuscarOpcionesRender;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/cartaspresentacion/cartas_presentacion_buscar_data');
$payload = CartasPresentacionBuscarOpcionesRender::enrich(CartaspresentacionPayload::postData($data));
$view = CartaspresentacionPayload::buscarViewFromPayload($payload);

$oDesplRegion = Desplegable::desdeOpciones($view['opciones_region'], 'region');
$oDesplPais = new Desplegable();
$oDesplPais->setOpciones($view['opciones_pais']);
$oDesplPais->setNombre('pais');
$oDesplDelegacion = Desplegable::desdeOpciones($view['opciones_delegacion'], 'dl');

$a_campos = [
    'oPosicion' => $oPosicion,
    'hash_lista_html' => $view['hash_lista_html'],
    'url_lista' => $view['url_lista'],
    'oDesplRegion' => $oDesplRegion,
    'oDesplPais' => $oDesplPais,
    'oDesplDelegacion' => $oDesplDelegacion,
];

$oView = new ViewNewPhtml('frontend\\cartaspresentacion\\view');
$oView->renderizar('cartas_presentacion_buscar.phtml', $a_campos);
