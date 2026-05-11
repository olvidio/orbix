<?php

use src\actividades\application\TipoActivFormModificar;
use src\shared\web\ContestarJson;

$useCase = new TipoActivFormModificar();
$html = $useCase->execute($_POST);

ContestarJson::enviar('', ['html' => $html]);
