<?php

/**
 * Documentos de un centro (`id_ubi`) y/o lugar (`id_lugar`) para trasladarlos.
 * La lógica está en {@see \src\inventario\application\ListaDocsDeCtrData}.
 */

use src\inventario\application\ListaDocsDeCtrData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListaDocsDeCtrData $useCase */
$useCase = DependencyResolver::get(ListaDocsDeCtrData::class);
ContestarJson::enviar('', $useCase->execute(
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_lugar'),
));
