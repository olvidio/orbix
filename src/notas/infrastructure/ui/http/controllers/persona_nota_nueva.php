<?php

use src\notas\application\PersonaNotaNueva;
use frontend\shared\web\ContestarJson;

/**
 * Crea una `PersonaNota`. Responde JSON `{success, mensaje, data}`.
 */
$error_txt = PersonaNotaNueva::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
