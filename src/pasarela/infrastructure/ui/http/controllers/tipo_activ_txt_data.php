<?php

use frontend\shared\web\ContestarJson;
use src\pasarela\application\TipoActivTxtData;

$id_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');

$data = TipoActivTxtData::execute($id_tipo_activ);
ContestarJson::enviar('', $data);
