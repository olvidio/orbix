<?php

declare(strict_types=1);

namespace Tests\unit\actividadescentro\application\services;

use src\actividadescentro\application\services\CentroDeEncargoLookup;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use Tests\myTest;

/** Centro de un encargo por su id: el primer dígito dice si es de la sección de la sesión (sv en los tests) o de la otra. */
final class CentroDeEncargoLookupTest extends myTest
{
    public function test_un_centro_de_la_seccion_de_la_sesion_sale_de_los_centros_de_la_dl(): void
    {
        $centro = $this->createMock(CentroDl::class);
        $dl = $this->createMock(CentroDlRepositoryInterface::class);
        $dl->expects($this->once())->method('findById')->with(10018158)->willReturn($centro);
        $ellas = $this->createMock(CentroEllasRepositoryInterface::class);
        $ellas->expects($this->never())->method('findById');
        $ellos = $this->createMock(CentroEllosRepositoryInterface::class);
        $ellos->expects($this->never())->method('findById');

        $this->assertSame($centro, (new CentroDeEncargoLookup($dl, $ellas, $ellos))->findById('10018158'));
    }

    public function test_un_centro_de_la_otra_seccion_sale_de_su_copia(): void
    {
        $centro = $this->createMock(CentroEllas::class);
        $dl = $this->createMock(CentroDlRepositoryInterface::class);
        $dl->expects($this->never())->method('findById');
        $ellas = $this->createMock(CentroEllasRepositoryInterface::class);
        $ellas->expects($this->once())->method('findById')->with(20018158)->willReturn($centro);
        $ellos = $this->createMock(CentroEllosRepositoryInterface::class);

        $this->assertSame($centro, (new CentroDeEncargoLookup($dl, $ellas, $ellos))->findById(20018158));
    }

    public function test_un_id_que_no_es_un_numero_no_busca_nada(): void
    {
        $dl = $this->createMock(CentroDlRepositoryInterface::class);
        $dl->expects($this->never())->method('findById');
        $ellas = $this->createMock(CentroEllasRepositoryInterface::class);
        $ellas->expects($this->never())->method('findById');
        $lookup = new CentroDeEncargoLookup($dl, $ellas, $this->createMock(CentroEllosRepositoryInterface::class));

        $this->assertNull($lookup->findById(''));
        $this->assertNull($lookup->findById(null));
        $this->assertNull($lookup->findById(0));
    }
}
