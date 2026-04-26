<?php

use src\encargossacd\application\SacdAusenciasGetData;
use frontend\shared\web\ContestarJson;

$id_nom = (int)(filter_input(INPUT_POST, 'id_nom') ?? filter_input(INPUT_GET, 'id_nom') ?? 0);
$historial = (int)(filter_input(INPUT_POST, 'historial') ?? filter_input(INPUT_GET, 'historial') ?? 0);

ContestarJson::enviar('', SacdAusenciasGetData::execute($id_nom, $historial));
