<?php
/**
 * Endpoint backend AJAX: guarda la edicion de una actividad existente.
 * Abre `ctx_editar` (HashB, `{id_activ}`) emitido por `actividad_ver_datos`.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'editar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\actividades\application\ActividadEditar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_editar'),
        'actividad_editar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['id_activ'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ');

/** @var ActividadEditar $useCase */
$useCase = DependencyResolver::get(ActividadEditar::class);
$result = $useCase->execute($input);

if (isset($result['tipo_error'])) {
    ContestarJson::enviar($result['error_txt']);
    exit;
}

ContestarJson::enviar($result['error_txt']);
