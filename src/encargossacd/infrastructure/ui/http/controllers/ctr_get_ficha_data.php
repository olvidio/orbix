<?php

use src\encargossacd\application\CtrGetFichaData;
use web\ContestarJson;

$id_ubi = (int)(filter_input(INPUT_POST, 'id_ubi') ?? filter_input(INPUT_GET, 'id_ubi') ?? 0);
$seleccion_sacd = (int)(filter_input(INPUT_POST, 'seleccion_sacd') ?? filter_input(INPUT_GET, 'seleccion_sacd') ?? 0);

ContestarJson::enviar('', CtrGetFichaData::execute($id_ubi, $seleccion_sacd));
