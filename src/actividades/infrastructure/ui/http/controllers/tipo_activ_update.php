<?php

use src\actividades\application\TipoActivUpdate;
use frontend\shared\web\ContestarJson;

$useCase = new TipoActivUpdate();
$mensaje = $useCase->execute($_POST);

ContestarJson::enviar('', ['mensaje' => $mensaje]);
