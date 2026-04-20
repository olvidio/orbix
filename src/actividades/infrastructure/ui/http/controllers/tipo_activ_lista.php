<?php

use src\actividades\application\TipoActivLista;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new TipoActivLista();
echo $useCase->execute($_POST);
