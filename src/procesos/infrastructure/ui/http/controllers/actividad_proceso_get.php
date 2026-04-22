<?php

use src\procesos\application\ActividadProcesoGet;
use web\ContestarJson;

$useCase = new ActividadProcesoGet();
ContestarJson::enviar('', $useCase->execute($_POST));
