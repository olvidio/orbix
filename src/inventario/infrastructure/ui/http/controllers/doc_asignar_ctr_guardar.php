<?php

/**
 * Guarda la asignación de un tipo de documento (el de la cápsula) a los centros de `str_selected_id`,
 * con el número de cada uno en `num_<id>`.
 * La lógica está en {@see \src\inventario\application\DocumentosAsignar::aCentros()}.
 */

use src\inventario\application\DocumentosAsignar;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(FuncTablasSupport::inputString($_POST, 'ctx_guardar'), 'doc_asignar_ctr_guardar');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$seleccion = json_decode(rawurldecode(FuncTablasSupport::inputString($_POST, 'str_selected_id')), true);
$numeros = [];
foreach (is_array($seleccion) ? $seleccion : [] as $id) {
    if (is_numeric($id)) {
        $numeros[(int) $id] = $_POST['num_' . (int) $id] ?? 0;
    }
}

/** @var DocumentosAsignar $useCase */
$useCase = DependencyResolver::get(DocumentosAsignar::class);
ContestarJson::enviar($useCase->aCentros(
    FuncTablasSupport::inputInt($ctx, 'id_tipo_doc'),
    $numeros,
    FuncTablasSupport::inputString($_POST, 'f_recibido'),
    FuncTablasSupport::inputString($_POST, 'f_asignado'),
), 'ok');
