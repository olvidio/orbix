<?php
/**
 * Endpoint backend: devuelve el payload JSON estandar de desplegable
 * (`id`, `opciones`, `selected`, `blanco`, `val_blanco`) con los
 * posibles propietarios de plaza para la persona+actividad indicadas.
 *
 * El `<select>` se construye en cliente via
 * `fnjs_construir_desplegable`. Sustituye la rama `lst_propietarios`
 * del dispatcher legacy `gestion_plazas_ajax.php`, que devolvia HTML.
 */

use src\actividadplazas\application\PosiblesPropietariosData;
use frontend\shared\web\ContestarJson;

$input = [
    'id_nom' => (int)filter_input(INPUT_POST, 'id_nom'),
    'id_activ' => (int)filter_input(INPUT_POST, 'id_activ'),
];

$data = PosiblesPropietariosData::execute($input);
$error = (string)($data['error'] ?? '');
if ($error !== '') {
    ContestarJson::enviar($error, 'ko');
    return;
}
ContestarJson::enviar('', $data);
