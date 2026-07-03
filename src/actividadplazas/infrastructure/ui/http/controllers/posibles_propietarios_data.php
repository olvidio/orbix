<?php

use src\shared\domain\helpers\FuncTablasSupport;

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
$input = [
    'id_nom' => FuncTablasSupport::inputInt($_POST, 'id_nom'),
    'id_activ' => FuncTablasSupport::inputInt($_POST, 'id_activ'),
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
