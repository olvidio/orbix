<?php

use src\encargossacd\application\SacdAusenciasJefeZonaData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', SacdAusenciasJefeZonaData::execute());
