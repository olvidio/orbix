<?php
/**
 * Endpoint JSON: sincroniza las `CambioUsuarioPropiedadPref` para un
 * `CambioUsuarioObjetoPref`. Crea, actualiza o elimina en funcion del POST.
 */

use src\cambios\application\CambioUsuarioPropiedadPrefGuardarTodas;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

/** @var array<string, mixed> $input */
$input = $_POST;

/** @var CambioUsuarioPropiedadPrefGuardarTodas $useCase */
$useCase = DependencyResolver::get(CambioUsuarioPropiedadPrefGuardarTodas::class);
$result = $useCase->execute($input);
$error = (string)$result['error'];

ContestarJson::enviar($error, []);
