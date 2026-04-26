<?php

use src\dossiers\application\PermDossiersListaData;
use frontend\shared\web\ContestarJson;

$Qtipo = (string)($_POST['tipo'] ?? '');

$tipo = $Qtipo === '' ? 'p' : $Qtipo;
$data = PermDossiersListaData::build($tipo);
ContestarJson::enviar('', $data);
