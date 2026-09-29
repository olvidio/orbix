<?php

use src\encargossacd\application\ListasComTxtUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'listas_com_txt_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var ListasComTxtUpdate $useCase */
$useCase = DependencyResolver::get(ListasComTxtUpdate::class);


$clave = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'clave');
$idioma = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'idioma');
$comunicacion = (string)(\src\shared\domain\helpers\FilterPostGet::post('comunicacion') ?? '');

ContestarJson::enviar('', $useCase->execute($clave, $idioma, $comunicacion));
