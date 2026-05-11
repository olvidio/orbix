<?php

use src\actividades\application\TipoActivUpdate;
use src\shared\web\ContestarJson;

$useCase = new TipoActivUpdate();
$mensaje = $useCase->execute($_POST);

ContestarJson::enviar('', ['mensaje' => $mensaje]);
