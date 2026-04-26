<?php

use src\encargossacd\application\EncargoHorarioSelectData;
use frontend\shared\web\ContestarJson;

$id_enc = (int)(filter_input(INPUT_POST, 'id_enc') ?? filter_input(INPUT_GET, 'id_enc') ?? 0);

ContestarJson::enviar('', EncargoHorarioSelectData::execute($id_enc));
