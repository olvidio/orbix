<?php

/**
 * Filas del formulario para asignar el tipo `id_tipo_doc` a los lugares de la dl elegidos (`sel`).
 * La lógica está en {@see \src\inventario\application\DocAsignarListaData::paraLugares()}.
 */

use src\inventario\application\DocAsignarListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var DocAsignarListaData $useCase */
$useCase = DependencyResolver::get(DocAsignarListaData::class);
ContestarJson::enviar('', $useCase->paraLugares(
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_tipo_doc'),
    array_values((array) \src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)),
));
