<?php

use frontend\shared\helpers\PayloadCoercion;

use frontend\shared\FrontBootstrap;
use frontend\shared\PostRequest;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$data = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_aprobar');
echo PayloadCoercion::string($data['text'] ?? _('Hecho!'));
