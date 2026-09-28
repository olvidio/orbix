<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\AnadirCtrTarea;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$que = (string) \src\shared\domain\helpers\FilterPostGet::post('que');

try {
    if ($que === 'quitar') {
        $ctx = HashB::open(
            \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_quitar'),
            'quitar_ctr_tarea'
        );
        $id_item = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item');
        $id_ubi = 0;
        $id_tarea = 0;
    } elseif ($que === 'anadir') {
        HashB::open(
            \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_anadir'),
            'anadir_ctr_tarea'
        );
        $id_ubi = \src\shared\domain\helpers\FilterPostGet::post('id_ubi');
        $id_tarea = \src\shared\domain\helpers\FilterPostGet::post('id_tarea');
        $id_item = 0;
    } else {
        ContestarJson::enviar(_("Operación no autorizada"), 'none');
        return;
    }
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var AnadirCtrTarea $useCase */
$useCase = DependencyResolver::get(AnadirCtrTarea::class);
$result = $useCase->execute([
    'que' => $que,
    'id_ubi' => $id_ubi,
    'id_tarea' => $id_tarea,
    'id_item' => $id_item,
]);

ContestarJson::enviar($result['error']);
