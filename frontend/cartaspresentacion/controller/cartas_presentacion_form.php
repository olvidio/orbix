<?php
/**
 * Controlador AJAX HTML: formulario modal de modificacion de una
 * `CartaPresentacion`.
 *
 * Delega en `/src/cartaspresentacion/carta_presentacion_form_data`;
 * {@see \frontend\cartaspresentacion\helpers\CartaPresentacionFormRender} compone `hash_update_html`.
 * renderiza `cartas_presentacion_form.phtml`. Sucesor de la rama
 * `que_mod=form_pres` del dispatcher legacy `cartas_presentacion_ajax.php`.
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\cartaspresentacion\helpers\CartaPresentacionFormRender;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/cartaspresentacion_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$campos = [
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'id_direccion' => (int)filter_input(INPUT_POST, 'id_direccion'),
];

$data = PostRequest::getDataFromUrl('/src/cartaspresentacion/carta_presentacion_form_data', $campos);
$payload = CartaPresentacionFormRender::enrich(cartaspresentacion_post_data($data));
$a_campos = cartaspresentacion_form_view_from_payload($payload);

$oView = new ViewNewPhtml('frontend\\cartaspresentacion\\view');
$oView->renderizar('cartas_presentacion_form.phtml', $a_campos);
