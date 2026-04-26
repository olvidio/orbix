<?php

use src\actividades\application\TipoActivLista;
use frontend\shared\web\ContestarJson;

$useCase = new TipoActivLista();
$html = $useCase->execute($_POST);

ContestarJson::enviar('', ['html' => $html]);
