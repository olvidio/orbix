<?php

use src\encargossacd\application\PropuestasAprobar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_aprobar'),
        'propuestas_aprobar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var PropuestasAprobar $useCase */
$useCase = DependencyResolver::get(PropuestasAprobar::class);
ContestarJson::enviar('', $useCase->execute());
