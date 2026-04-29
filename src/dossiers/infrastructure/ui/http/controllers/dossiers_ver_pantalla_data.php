<?php

use frontend\shared\web\ContestarJson;
use src\dossiers\application\DossiersVerPantallaData;

$result = DossiersVerPantallaData::build($_POST);
$error = (string)($result['error'] ?? '');
unset($result['error']);

// El backend devuelve sólo datos/link_specs. La firma de URLs con HashFront se hace
// en el frontend (ver frontend/dossiers/controller/dossiers_ver.php).
ContestarJson::enviar($error, $result);
