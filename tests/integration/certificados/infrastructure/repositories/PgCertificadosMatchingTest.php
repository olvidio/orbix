<?php

namespace Tests\integration\certificados\infrastructure\repositories;

use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\criteria\CertificadoRecibidoField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\factories\certificados\CertificadoRecibidoFactory;
use Tests\myTest;

/**
 * `matching()` de PgCertificadoRecibidoRepository (paso 4.1 de docs/dev/refactor_busquedas_criteria.md).
 * El de emitidos está en {@see PgCertificadoEmitidoMatchingTest} (necesita conexión a una región con la tabla).
 */
class PgCertificadosMatchingTest extends myTest
{
    public function test_recibido_matching_por_item_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(CertificadoRecibidoRepositoryInterface::class);
        $oCertificado = (new CertificadoRecibidoFactory())->createSimple();
        $idItem = $oCertificado->getId_item();
        $repo->Guardar($oCertificado);

        $criteria = Criteria::create()
            ->where(Filter::eq(CertificadoRecibidoField::IdItem, $idItem))
            ->orderBy(CertificadoRecibidoField::FCertificado);
        $nuevo = $repo->matching($criteria);
        $legacy = $repo->getCertificados(['id_item' => $idItem, '_ordre' => 'f_certificado']);

        $this->assertCount(1, $nuevo);
        $this->assertEquals($legacy, $nuevo);

        $repo->Eliminar($nuevo[0]);
        $this->assertSame([], $repo->matching($criteria));
    }
}
