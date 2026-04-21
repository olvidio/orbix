<?php

use src\misas\application\CambiarStatusPantallaData;
use web\ContestarJson;

try {
    ContestarJson::enviar('', CambiarStatusPantallaData::getData());
} catch (\RuntimeException $e) {
    ContestarJson::enviar($e->getMessage());
}
