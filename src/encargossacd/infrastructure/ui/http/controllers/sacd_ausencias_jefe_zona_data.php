<?php

use src\encargossacd\application\SacdAusenciasJefeZonaData;
use web\ContestarJson;

ContestarJson::enviar('', SacdAusenciasJefeZonaData::execute());
