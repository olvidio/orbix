<?php

use src\notas\application\PersonaNotaEditar;
use src\notas\application\PersonaNotaEliminar;
use src\notas\application\PersonaNotaNueva;

/**
 * Shim de compatibilidad para `form_1011.phtml` y `select1011.phtml`.
 *
 * @deprecated Usar los endpoints granulares:
 *   - `src/notas/infrastructure/ui/http/controllers/persona_nota_nueva.php`
 *   - `src/notas/infrastructure/ui/http/controllers/persona_nota_editar.php`
 *   - `src/notas/infrastructure/ui/http/controllers/persona_nota_eliminar.php`
 *
 * El contrato de respuesta se mantiene: texto plano con el mensaje de error
 * (o vacio si la operacion ha tenido exito). La migracion al contrato JSON
 * `ContestarJson` ocurrira cuando se porten `form_1011.phtml` y
 * `select1011.phtml` (slice 4).
 */
require_once 'apps/core/global_header.inc';
require_once 'apps/core/global_object.inc';

$Qpau = (string)filter_input(INPUT_POST, 'pau');
if ($Qpau !== 'p') {
    exit('OJO: pau no es de persona');
}

$Qmod = (string)filter_input(INPUT_POST, 'mod');
switch ($Qmod) {
    case 'eliminar':
        $msg_err = PersonaNotaEliminar::execute($_POST);
        break;
    case 'nuevo':
        $msg_err = PersonaNotaNueva::execute($_POST);
        break;
    case 'editar':
        $msg_err = PersonaNotaEditar::execute($_POST);
        break;
    default:
        $msg_err = '';
}

if (!empty($msg_err)) {
    echo $msg_err;
}
