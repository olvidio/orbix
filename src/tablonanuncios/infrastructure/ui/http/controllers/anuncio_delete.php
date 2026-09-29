<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\tablonanuncios\application\AnuncioDelete;

/** @var AnuncioDelete $useCase */
$useCase = DependencyResolver::get(AnuncioDelete::class);

$a_sel = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'sel');
if ($a_sel === []) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

try {
    $ctx = HashB::open((string)$a_sel[0], 'anuncio_delete');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Quuid_item = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'uuid_item');

$error_txt = $useCase->execute($Quuid_item);

ContestarJson::enviar($error_txt, 'ok');
