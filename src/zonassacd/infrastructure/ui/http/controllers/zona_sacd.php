<?php

use src\zonassacd\application\ZonaSacdPage;
use web\ContestarJson;

$jsondata = ContestarJson::respuestaPhp('', ZonaSacdPage::getData());
ContestarJson::send($jsondata);
