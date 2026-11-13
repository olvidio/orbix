<?php

/**
 * Centros sin lugares, para asignar el tipo `id_tipo_doc` o (con `inventario`) imprimir el inventario.
 * La lógica está en {@see \src\inventario\application\LugaresDeDlbData::centros()}.
 */

use src\inventario\application\LugaresDeDlbData;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var LugaresDeDlbData $useCase */
$useCase = DependencyResolver::get(LugaresDeDlbData::class);
ContestarJson::enviar('', $useCase->centros(FuncTablasSupport::inputInt($_POST, 'id_tipo_doc'), !empty(FuncTablasSupport::inputInt($_POST, 'inventario'))));
