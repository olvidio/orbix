<?php

use src\encargossacd\application\SacdAusenciasJefeZonaData;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', SacdAusenciasJefeZonaData::execute());
