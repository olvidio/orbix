<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\pasarela\application\ActivacionExcepcionGuardar;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_guardar'),
        'activacion_excepcion_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$modo = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'modo');
if ($modo === 'existing') {
    // Edición de una fila ya cargada: la identidad viene atada al ctx, no del POST.
    $id_tipo_activ = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_tipo_activ');
} else {
    // Alta nueva: el id_tipo_activ lo compone el propio formulario (selector
    // ActividadTipo); el ctx solo prueba que la petición viene del formulario 'nuevo'.
    $id_tipo_activ = (string)\src\shared\domain\helpers\FilterPostGet::post('id_tipo_activ');
}
$valor = (string)\src\shared\domain\helpers\FilterPostGet::post('valor');

/** @var ActivacionExcepcionGuardar $useCase */
$useCase = DependencyResolver::get(ActivacionExcepcionGuardar::class);

$error_txt = $useCase->execute($id_tipo_activ, $valor);
ContestarJson::enviar($error_txt, 'ok');
