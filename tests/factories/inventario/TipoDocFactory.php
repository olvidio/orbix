<?php

namespace Tests\factories\inventario;

use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\entity\TipoDoc;

/**
 * Factory para crear instancias de TipoDoc para tests
 */
class TipoDocFactory
{
    public function createSimple(?int $id = null): TipoDoc
    {
        if ($id === null) {
            $repository = $GLOBALS['container']->get(TipoDocRepositoryInterface::class);
            $id = $repository->getNewId();
        }

        $oTipoDoc = new TipoDoc();
        $oTipoDoc->setId_tipo_doc($id);
        $oTipoDoc->setSigla('tst');
        $oTipoDoc->setNom_doc('test tipodoc ' . $id);
        $oTipoDoc->setNumerado(false);
        $oTipoDoc->setVigente(true);
        $oTipoDoc->setBajo_llave(false);

        return $oTipoDoc;
    }
}
