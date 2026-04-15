<?php

use frontend\shared\PostRequest;

require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/ubis/ubis_guardar', $_POST);
if (!empty($data['error'])) {
    echo $data['error'];
}
