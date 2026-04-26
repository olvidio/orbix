<?php

use src\procesos\application\FasesActivCambioLista;
use frontend\shared\web\ContestarJson;

$useCase = new FasesActivCambioLista();
ContestarJson::enviar('', $useCase->execute($_POST));
