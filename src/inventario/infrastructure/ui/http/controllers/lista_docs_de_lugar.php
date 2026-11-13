<?php

/**
 * Documentos del lugar `id_lugar` usado como maleta (con las cápsulas del grupo del equipaje).
 * La lógica está en {@see \src\inventario\application\DocumentosDeLugarData}.
 */

use src\inventario\application\DocumentosDeLugarData;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var DocumentosDeLugarData $useCase */
$useCase = DependencyResolver::get(DocumentosDeLugarData::class);
ContestarJson::enviar('', $useCase->execute(
    FuncTablasSupport::inputInt($_POST, 'id_lugar'),
    FuncTablasSupport::inputInt($_POST, 'id_grupo'),
    FuncTablasSupport::inputInt($_POST, 'id_equipaje'),
    FuncTablasSupport::inputInt($_POST, 'id_item_egm'),
));
