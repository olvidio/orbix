<?php

use src\procesos\application\TipoActivProcesoAsignar;
use web\ContestarJson;

$useCase = new TipoActivProcesoAsignar();
$error = $useCase->execute($_POST);
ContestarJson::enviar($error);
