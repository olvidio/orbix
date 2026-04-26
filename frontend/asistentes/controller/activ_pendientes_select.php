<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;

require_once 'frontend/shared/global_header_front.inc';

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/activ_pendientes_select_data', $campos);
$payload = is_array($data) ? $data : [];

(new ViewNewPhtml('frontend\\asistentes\\controller'))
    ->renderizar('activ_pendientes.phtml', $payload);
