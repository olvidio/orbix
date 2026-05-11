<?php

use src\usuarios\application\PreferenciaTablaData;
use src\shared\web\ContestarJson;

$id_tabla = (string)filter_input(INPUT_POST, 'id_tabla');

$data = PreferenciaTablaData::execute($id_tabla);

ContestarJson::enviar('', $data);
