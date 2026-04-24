<?php

use src\dossiers\application\TipoDossierEliminar;
use web\ContestarJson;

/**
 * Elimina un `TipoDossier`.
 * Responde JSON `{success, mensaje, data}`.
 */
$error_txt = TipoDossierEliminar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
