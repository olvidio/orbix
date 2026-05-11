<?php

use src\shared\web\ContestarJson;
use src\dbextern\application\RefrescarBduUseCase;

$error_txt = '';
try {
    $useCase = new RefrescarBduUseCase();
    $useCase();
} catch (Exception $e) {
    $error_txt = _("Error al refrescar la BDU") . ": " . $e->getMessage();
}

ContestarJson::enviar($error_txt, 'ok');
