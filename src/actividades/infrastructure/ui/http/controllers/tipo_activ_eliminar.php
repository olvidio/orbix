<?php

use src\actividades\application\TipoActivEliminar;
use src\shared\web\ContestarJson;

$useCase = new TipoActivEliminar();
$mensaje = $useCase->execute($_POST);

ContestarJson::enviar('', ['mensaje' => $mensaje]);
