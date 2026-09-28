<?php

use src\actividadestudios\application\DocenciaActualizar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_actualizar'),
        'docencia_actualizar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var DocenciaActualizar $useCase */
$useCase = DependencyResolver::get(DocenciaActualizar::class);
$txt_rta = $useCase->execute($_POST);
ContestarJson::enviar('', $txt_rta);
