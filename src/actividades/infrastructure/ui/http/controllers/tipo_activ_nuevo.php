<?php

use src\actividades\application\TipoActivNuevo;
use src\shared\web\ContestarJson;

$useCase = new TipoActivNuevo();
$mensaje = $useCase->execute($_POST);

ContestarJson::enviar('', ['mensaje' => $mensaje]);
