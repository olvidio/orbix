<?php

use src\dbextern\application\RefrescarBduUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error_txt = '';
try {
    DependencyResolver::get(RefrescarBduUseCase::class)();
} catch (Exception $e) {
    $error_txt = _("Error al refrescar la BDU") . ": " . $e->getMessage();
}

ContestarJson::enviar($error_txt, 'ok');
