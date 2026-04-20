<?php

use src\actividades\application\TipoActivFormNuevo;
use web\ContestarJson;

$useCase = new TipoActivFormNuevo();
$html = $useCase->execute($_POST);

ContestarJson::enviar('', ['html' => $html]);
