<?php

use src\procesos\application\FasesActivCambioLista;
use web\ContestarJson;

$useCase = new FasesActivCambioLista();
ContestarJson::enviar('', $useCase->execute($_POST));
