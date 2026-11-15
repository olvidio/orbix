<?php

use src\notas\application\TesseraCopiar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_copiar'),
        'tessera_copiar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$input = $_POST;
$input['id_nom_org'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom_org');
$error_txt = (DependencyResolver::get(TesseraCopiar::class))->execute($input);
ContestarJson::enviar($error_txt, 'ok');
