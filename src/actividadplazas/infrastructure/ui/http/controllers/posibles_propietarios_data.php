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
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;

$input = [
    'id_nom' => input_int($_POST, 'id_nom'),
    'id_activ' => input_int($_POST, 'id_activ'),
];

/** @var PosiblesPropietariosData $useCase */
$useCase = DependencyResolver::get(PosiblesPropietariosData::class);
$data = $useCase->execute($input);
$error = (string)($data['error'] ?? '');
if ($error !== '') {
    ContestarJson::enviar($error, 'ko');
    return;
}
ContestarJson::enviar('', $data);
