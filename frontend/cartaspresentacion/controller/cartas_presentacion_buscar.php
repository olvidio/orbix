<?php
/**
 * Pantalla frontend: formulario de busqueda de cartas de presentacion.
 *
 * Sucesor de `apps/cartaspresentacion/controller/cartas_presentacion_buscar.php`.
 * Los datos (opciones de region, pais, delegacion) se obtienen del
 * endpoint `/src/cartaspresentacion/cartas_presentacion_buscar_data` y
 * los `<select>` se montan en la vista con `web\Desplegable`.
 *
 * El form postea a `frontend/cartaspresentacion/controller/cartas_presentacion_lista.php`
 * con `que=get` y `poblacion/region/pais/dl` como filtros.
 */

use src\shared\config\ConfigGlobal;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use web\Desplegable;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$data = PostRequest::getDataFromUrl('/src/cartaspresentacion/cartas_presentacion_buscar_data');
$payload = is_array($data) ? $data : [];

$opciones_region = (array)($payload['opciones_region'] ?? []);
$opciones_pais = (array)($payload['opciones_pais'] ?? []);
$opciones_delegacion = (array)($payload['opciones_delegacion'] ?? []);

$oDesplRegion = Desplegable::desdeOpciones($opciones_region, 'region');
$oDesplPais = new Desplegable();
$oDesplPais->setOpciones($opciones_pais);
$oDesplPais->setNombre('pais');
$oDesplDelegacion = Desplegable::desdeOpciones($opciones_delegacion, 'dl');

$web = rtrim(ConfigGlobal::getWeb(), '/');
$url_lista = $web . '/frontend/cartaspresentacion/controller/cartas_presentacion_lista.php';

$oHash = new Hash();
$oHash->setUrl($url_lista);
$oHash->setArrayCamposHidden(['que' => 'get']);
$oHash->setCamposForm('que!poblacion!region!pais!dl');
$oHash->setCamposNo('scroll_id!sel');

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_lista' => $url_lista,
    'oDesplRegion' => $oDesplRegion,
    'oDesplPais' => $oDesplPais,
    'oDesplDelegacion' => $oDesplDelegacion,
];

$oView = new ViewNewPhtml('frontend\\cartaspresentacion\\controller');
$oView->renderizar('cartas_presentacion_buscar.phtml', $a_campos);
