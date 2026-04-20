<?php

use src\actividades\application\TipoActivFormNuevo;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new TipoActivFormNuevo();
echo $useCase->execute($_POST);
