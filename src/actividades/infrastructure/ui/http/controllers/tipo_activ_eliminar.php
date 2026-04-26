<?php

use src\actividades\application\TipoActivEliminar;
use frontend\shared\web\ContestarJson;

$useCase = new TipoActivEliminar();
$mensaje = $useCase->execute($_POST);

ContestarJson::enviar('', ['mensaje' => $mensaje]);
