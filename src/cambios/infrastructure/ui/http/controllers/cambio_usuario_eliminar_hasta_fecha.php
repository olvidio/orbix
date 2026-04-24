<?php
/**
 * Endpoint backend: elimina los `CambioUsuario` con fecha <= `f_fin`.
 */

use src\cambios\application\CambioUsuarioEliminarHastaFecha;
use web\ContestarJson;

$input = ['f_fin' => (string)filter_input(INPUT_POST, 'f_fin')];
$result = CambioUsuarioEliminarHastaFecha::execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', '');
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
