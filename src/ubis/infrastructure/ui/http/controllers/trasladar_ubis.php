<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\TrasladarUbis;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$capsules = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'ctx_trasladar');
$ids = [];
foreach ($capsules as $capsule) {
    try {
        $ctx = HashB::open((string) $capsule, 'trasladar_ubis');
    } catch (HashBInvalidException $e) {
        continue;
    }
    $id = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_ubi');
    if ($id > 0) {
        $ids[] = $id;
    }
}
if ($capsules === [] || $ids === []) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['sel'] = $ids;

$errorTxt = DependencyResolver::get(TrasladarUbis::class)->execute($input);
ContestarJson::enviar($errorTxt, ['ok' => true]);
