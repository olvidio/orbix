<?php

use frontend\shared\PostRequest;

require_once("frontend/shared/global_header_front.inc");

//En el caso de modificar cartas de presentación, quiero que quede dentro del bloque.
$oPosicion->recordar();

$data = PostRequest::getDataFromUrl('/src/ubis/trasladar_ubis', $_POST);
if (!empty($data['error'])) {
    exit((string)$data['error']);
}
