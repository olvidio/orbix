<?php
/**
 * Endpoint JSON: sincroniza las `CambioUsuarioPropiedadPref` para un
 * `CambioUsuarioObjetoPref`. Crea, actualiza o elimina en funcion del POST.
 */

use src\cambios\application\CambioUsuarioPropiedadPrefGuardarTodas;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar_propiedades'),
        'cambio_usuario_propiedad_pref_guardar_todas'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var array<string, mixed> $input */
$input = $_POST;
$input['id_item_usuario_objeto_prop'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt(
    $ctx,
    'id_item_usuario_objeto'
);

/** @var CambioUsuarioPropiedadPrefGuardarTodas $useCase */
$useCase = DependencyResolver::get(CambioUsuarioPropiedadPrefGuardarTodas::class);
$result = $useCase->execute($input);
$error = (string)$result['error'];

ContestarJson::enviar($error, []);
