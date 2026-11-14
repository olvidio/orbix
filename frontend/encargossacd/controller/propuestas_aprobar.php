<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\FrontBootstrap;
use frontend\shared\PostRequest;

/**
 * Aprueba las propuestas de encargos (pasa los cambios a real y borra las tablas de propuestas).
 *
 * Es una acción, no una pantalla: solo se ejecuta con la cápsula `ctx_aprobar` que firma el menú de
 * propuestas (`propuestas_menu.php`) y que llega en el enlace tras la confirmación. Abierta sin
 * ella, `/src/encargossacd/propuestas_aprobar` responde «Operación no autorizada».
 */
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$payload = PostRequest::requestPayloadForHash();
$data = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_aprobar', [
    'ctx_aprobar' => PayloadCoercion::string($payload['ctx_aprobar'] ?? ''),
]);
echo PayloadCoercion::string($data['text'] ?? _("Hecho!"));
