<?php

use src\actividades\application\TipoActivFormModificar;
use web\ContestarJson;

$useCase = new TipoActivFormModificar();
$html = $useCase->execute($_POST);

ContestarJson::enviar('', ['html' => $html]);
