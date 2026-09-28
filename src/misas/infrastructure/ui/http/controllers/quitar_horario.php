<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\QuitarHorarioPlantilla;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_quitar'),
        'quitar_horario'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var QuitarHorarioPlantilla $useCase */
$useCase = DependencyResolver::get(QuitarHorarioPlantilla::class);
$result = $useCase->execute([
    'id_item' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item'),
]);

ContestarJson::enviar($result['error']);
