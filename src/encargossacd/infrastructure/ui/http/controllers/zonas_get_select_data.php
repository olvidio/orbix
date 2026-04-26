<?php

use src\encargossacd\application\EncargoZonasSelectData;
use frontend\shared\web\ContestarJson;

$id_zona = filter_input(INPUT_POST, 'id_zona');
if ($id_zona === null) {
    $id_zona = filter_input(INPUT_GET, 'id_zona');
}

ContestarJson::enviar('', EncargoZonasSelectData::execute(
    $id_zona !== null && $id_zona !== false ? (int)$id_zona : 0
));
