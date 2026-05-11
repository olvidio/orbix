<?php

use src\misas\application\CambiarStatusPantallaData;
use src\shared\web\ContestarJson;

try {
    ContestarJson::enviar('', CambiarStatusPantallaData::getData());
} catch (\RuntimeException $e) {
    ContestarJson::enviar($e->getMessage());
}
