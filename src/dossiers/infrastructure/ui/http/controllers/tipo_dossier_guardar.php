<?php

use src\dossiers\application\TipoDossierGuardar;
use frontend\shared\web\ContestarJson;

/**
 * Guarda los cambios a un `TipoDossier`.
 * Responde JSON `{success, mensaje, data}`.
 */
$error_txt = TipoDossierGuardar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
