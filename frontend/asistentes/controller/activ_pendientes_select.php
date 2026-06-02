<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\asistentes\helpers\ActivPendientesSelectRender;

require_once 'frontend/shared/global_header_front.inc';

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/activ_pendientes_select_data', $campos);
/** @var array<string, mixed> $payload */
$payload = is_array($data) ? $data : [];
$payload = ActivPendientesSelectRender::enrich($payload);

(new ViewNewPhtml('frontend\\asistentes\\view'))
    ->renderizar('activ_pendientes.phtml', $payload);
