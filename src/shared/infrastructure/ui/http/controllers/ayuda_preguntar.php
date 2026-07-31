<?php

/**
 * Endpoint JSON: buscar en la documentación local (docs/ai + docs/manual) sin LLM.
 */

use src\shared\application\AyudaPreguntar;
use src\shared\web\ContestarJson;

$useCase = new AyudaPreguntar();
ContestarJson::enviar('', $useCase->execute($_POST));
