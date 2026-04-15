<?php

use src\zonassacd\application\ZonaCtrPage;
use web\ContestarJson;

$jsondata = ContestarJson::respuestaPhp('', ZonaCtrPage::getData());
ContestarJson::send($jsondata);
