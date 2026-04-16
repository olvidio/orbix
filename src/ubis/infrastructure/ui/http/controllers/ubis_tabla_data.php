<?php

use src\ubis\application\UbisTablaData;
use web\ContestarJson;

$data = UbisTablaData::execute($_POST);
if (isset($data['error'])) {
    ContestarJson::enviar((string)$data['error'], []);
    return;
}
ContestarJson::enviar('', $data);
