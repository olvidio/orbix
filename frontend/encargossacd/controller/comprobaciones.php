<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\encargossacd\helpers\EncargossacdPayload;

/**
 * Proxy AJAX: la lógica vive en {@see \src\encargossacd\application\EncargoComprobacionesCtr}
 * y se expone en `/src/encargossacd/comprobaciones_ctr`.
 */

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

// La cápsula la firma la pantalla de listas (`listas_index.php`); aquí solo se reenvía.
$data = PostRequest::getDataFromUrl('/src/encargossacd/comprobaciones_ctr', [
    'ctx_comprobar' => (string) filter_input(INPUT_POST, 'ctx_comprobar'),
]);
AjaxJsonSupport::response('', ['text' => EncargossacdPayload::comprobacionesTexto($data)]);
