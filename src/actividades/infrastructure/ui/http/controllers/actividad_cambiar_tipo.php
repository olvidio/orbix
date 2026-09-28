<?php
/**
 * Endpoint backend AJAX: cambia el tipo de una actividad existente.
 * Abre `ctx_cambiar_tipo` (HashB, `{id_activ}`) emitido por `actividad_ver_datos`.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'cambiar_tipo').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\actividades\application\ActividadCambiarTipo;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_cambiar_tipo'),
        'actividad_cambiar_tipo'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['id_activ'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ');

/** @var ActividadCambiarTipo $useCase */
$useCase = DependencyResolver::get(ActividadCambiarTipo::class);
$result = $useCase->execute($input);

if (isset($result['tipo_error'])) {
    ContestarJson::enviar($result['error_txt']);
    exit;
}

ContestarJson::enviar($result['error_txt']);
