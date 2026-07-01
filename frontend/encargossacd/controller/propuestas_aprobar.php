<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

use frontend\shared\FrontBootstrap;
use frontend\shared\PostRequest;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$data = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_aprobar');
echo tessera_imprimir_string($data['text'] ?? _('Hecho!'));
