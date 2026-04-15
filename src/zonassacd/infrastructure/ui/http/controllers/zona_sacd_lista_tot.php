<?php

use src\zonassacd\application\ZonaSacdListaTot;
use web\ContestarJson;

$jsondata = ContestarJson::respuestaPhp('', ZonaSacdListaTot::execute());
ContestarJson::send($jsondata);
