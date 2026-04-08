<?php

namespace Tests\factories\inventario;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\entity\Documento;

/**
 * Factory para crear instancias de Documento para tests.
 * Requiere un id_tipo_doc e id_ubi existentes.
 */
class DocumentoFactory
{
    public function createSimple(?int $id = null, ?int $idTipoDoc = null, ?int $idUbi = null): Documento
    {
        if ($id === null) {
            $repository = $GLOBALS['container']->get(DocumentoRepositoryInterface::class);
            $id = $repository->getNewId();
        }

        $oDocumento = new Documento();
        $oDocumento->setId_doc($id);
        $oDocumento->setId_tipo_doc($idTipoDoc ?? 1);
        $oDocumento->setId_ubi($idUbi ?? 1);

        return $oDocumento;
    }
}
