<?php

use src\procesos\application\FasesActivCambioGet;
use web\ContestarJson;

$useCase = new FasesActivCambioGet();
ContestarJson::enviar('', $useCase->execute($_POST));
