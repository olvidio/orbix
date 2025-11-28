<?php

use src\usuarios\application\usuariosRegionContactos;
use web\ContestarJson;

$Qregion = (string)filter_input(INPUT_POST, 'region');

$error_txt = '';

$MailsRegion = new usuariosRegionContactos();
$data = $MailsRegion->usuariosRegionContactos($Qregion);

$error_txt = $data['error_txt'] ?? '';

// envía una Response
ContestarJson::enviar($error_txt, $data);