<?php

use frontend\shared\web\ContestarJson;
use src\pasarela\application\ContribucionNoDuermeLista;

$data = ContribucionNoDuermeLista::execute();
ContestarJson::enviar('', $data);
