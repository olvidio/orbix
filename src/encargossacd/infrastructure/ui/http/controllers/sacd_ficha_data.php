<?php

use src\encargossacd\application\SacdFichaData;
use web\ContestarJson;

$id_nom = (int)(filter_input(INPUT_POST, 'id_nom') ?? filter_input(INPUT_GET, 'id_nom') ?? 0);

ContestarJson::enviar('', SacdFichaData::execute($id_nom));
