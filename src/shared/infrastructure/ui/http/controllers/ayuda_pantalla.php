<?php

/**
 * Endpoint JSON: ayuda contextual de una pantalla (tareas del manual con ancla).
 * Entrada: `controller` (ruta o URL de la pantalla), `limite` (opcional).
 */

use src\shared\application\AyudaPantallaData;
use src\shared\web\ContestarJson;

$useCase = new AyudaPantallaData();
ContestarJson::enviar('', $useCase->execute($_POST));
