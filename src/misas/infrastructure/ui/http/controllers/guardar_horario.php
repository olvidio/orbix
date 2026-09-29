<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\GuardarHorarioTarea;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'guardar_horario'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var GuardarHorarioTarea $useCase */
$useCase = DependencyResolver::get(GuardarHorarioTarea::class);
$result = $useCase->execute([
    'id_item_h' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item_h'),
    't_start' => \src\shared\domain\helpers\FilterPostGet::post('t_start'),
    't_end' => \src\shared\domain\helpers\FilterPostGet::post('t_end'),
]);

ContestarJson::enviar($result['error']);
