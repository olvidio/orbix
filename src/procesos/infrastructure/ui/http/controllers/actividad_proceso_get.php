<?php

use src\procesos\application\ActividadProcesoGet;
use src\shared\web\ContestarJson;

$useCase = new ActividadProcesoGet();
ContestarJson::enviar('', $useCase->execute($_POST));
