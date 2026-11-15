<?php

use src\notas\application\ActaPdfEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_pdf_eliminar'),
        'acta_pdf_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$input = $_POST;
$input['acta_num'] = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'acta');
$error_txt = (DependencyResolver::get(ActaPdfEliminar::class))->execute($input);
ContestarJson::enviar($error_txt, 'ok');
