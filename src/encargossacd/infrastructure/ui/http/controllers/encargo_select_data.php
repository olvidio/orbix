<?php

use src\encargossacd\application\EncargoSelectData;
use web\ContestarJson;

$desc_enc = (string)(filter_input(INPUT_POST, 'desc_enc') ?? filter_input(INPUT_GET, 'desc_enc') ?? '');
$id_tipo_enc = (int)(filter_input(INPUT_POST, 'id_tipo_enc') ?? filter_input(INPUT_GET, 'id_tipo_enc') ?? 0);

ContestarJson::enviar('', EncargoSelectData::execute($desc_enc, $id_tipo_enc));
