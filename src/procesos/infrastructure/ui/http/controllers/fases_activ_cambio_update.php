<?php

use src\procesos\application\FasesActivCambioUpdate;
use src\shared\web\ContestarJson;

$useCase = new FasesActivCambioUpdate();
$error = $useCase->execute($_POST);
ContestarJson::enviar($error);
