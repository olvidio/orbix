<?php

namespace Tests\integration\certificados\infrastructure\repositories;

use PDO;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\criteria\CertificadoEmitidoField;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use Tests\factories\certificados\CertificadoEmitidoFactory;
use Tests\myTest;

/**
 * `matching()` de PgCertificadoEmitidoRepository (paso 4.1 de docs/dev/refactor_busquedas_criteria.md).
 * Como `PgCertificadoEmitidoRepositoryTest`, usa la conexión de una región que tiene `e_certificados_rstgr`.
 */
class PgCertificadoEmitidoMatchingTest extends myTest
{
    private CertificadoEmitidoRepositoryInterface $repository;
    private mixed $session_org;

    public function setUp(): void
    {
        parent::setUp();
        putenv('UBICACION=sv');
        $this->session_org = $_SESSION['session_auth']['esquema'];
        $_SESSION['session_auth']['esquema'] = 'H-Hv';

        $this->repository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);
        $oDBdst = $this->setConexion('H-Hv');
        $this->repository->setoDbl($oDBdst);
        $this->repository->setoDbl_select($oDBdst);
    }

    protected function tearDown(): void
    {
        $_SESSION['session_auth']['esquema'] = $this->session_org;
        parent::tearDown();
    }

    /** Copiado de PgCertificadoEmitidoRepositoryTest (a su vez de TrasladoDl). */
    private function setConexion(string $esquema): PDO
    {
        if (ConfigGlobal::mi_sfsv() === 2) {
            $database = 'sf';
            if (ConfigGlobal::mi_region_dl() !== $esquema) {
                $esquema = 'restof';
            }
        } else {
            $database = 'sv';
            $aEsquemas = (new DBPropiedades())->array_posibles_esquemas();
            $aEsquemas['H-Hv'] = 'H-Hv';
            if (!in_array($esquema, $aEsquemas, true)) {
                $esquema = 'restov';
            }
        }

        $config = (new ConfigDB($database))->getEsquema($esquema);

        return (new DBConnection($config))->getPDO();
    }

    public function test_matching_por_item_y_fechas_equivale_a_legacy(): void
    {
        $oCertificado = (new CertificadoEmitidoFactory())->createSimple();
        $idItem = $oCertificado->getId_item();
        $this->repository->Guardar($oCertificado);

        $criteria = Criteria::create()
            ->where(Filter::eq(CertificadoEmitidoField::IdItem, $idItem))
            ->andWhere(Filter::between(CertificadoEmitidoField::FCertificado, '2023-01-01', '2024-12-31'))
            ->andWhere(Filter::isNotNull(CertificadoEmitidoField::FEnviado))
            ->orderBy(CertificadoEmitidoField::FCertificado, OrderDirection::Desc)
            ->addOrderBy(CertificadoEmitidoField::Certificado, OrderDirection::Desc);
        $nuevo = $this->repository->matching($criteria);

        $this->assertCount(1, $nuevo);
        $this->assertSame('2024-01-01', $nuevo[0]->getF_certificado()->getIso());

        $sinEnviar = $this->repository->matching(
            Criteria::create()
                ->where(Filter::eq(CertificadoEmitidoField::IdItem, $idItem))
                ->andWhere(Filter::isNull(CertificadoEmitidoField::FEnviado))
        );
        $this->assertSame([], $sinEnviar);

        $this->repository->Eliminar($nuevo[0]);
    }
}
