<?php

/**
 * Lugares de los centros de la dl, por centro: para asignar el tipo `id_tipo_doc` o, con `inventario`,
 * para imprimir el inventario. La lógica está en {@see \src\inventario\application\LugaresDeDlbData}.
 */

use src\inventario\application\LugaresDeDlbData;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var LugaresDeDlbData $useCase */
$useCase = DependencyResolver::get(LugaresDeDlbData::class);
ContestarJson::enviar('', $useCase->execute(
    FuncTablasSupport::inputInt($_POST, 'id_tipo_doc'),
    !empty(FuncTablasSupport::inputInt($_POST, 'inventario')),
));
