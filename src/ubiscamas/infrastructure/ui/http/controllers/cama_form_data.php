<?php

use src\ubiscamas\application\CamaFormData;
use frontend\shared\web\ContestarJson;

$input = array_merge($_GET, $_POST);
$data = CamaFormData::build($input);
ContestarJson::enviar('', $data);
