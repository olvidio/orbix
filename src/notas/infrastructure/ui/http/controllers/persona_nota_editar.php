<?php

use src\notas\application\PersonaNotaEditar;
use frontend\shared\web\ContestarJson;

/**
 * Edita una `PersonaNota` existente. Responde JSON `{success, mensaje, data}`.
 */
$error_txt = PersonaNotaEditar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
