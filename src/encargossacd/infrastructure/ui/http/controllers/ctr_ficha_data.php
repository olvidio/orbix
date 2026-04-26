<?php

use src\encargossacd\application\CtrFichaData;
use frontend\shared\web\ContestarJson;

$id_ubi = (int)(filter_input(INPUT_POST, 'id_ubi') ?? filter_input(INPUT_GET, 'id_ubi') ?? 0);
$filtro_ctr = (int)(filter_input(INPUT_POST, 'filtro_ctr') ?? filter_input(INPUT_GET, 'filtro_ctr') ?? 0);

ContestarJson::enviar('', CtrFichaData::execute($id_ubi, $filtro_ctr));
