<?php

use src\procesos\application\FasesActivCambioGet;
use src\shared\web\ContestarJson;

$useCase = new FasesActivCambioGet();
ContestarJson::enviar('', $useCase->execute($_POST));
