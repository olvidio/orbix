<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use src\cambios\application\CambioParaAvisoLookup;
use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\entity\Cambio;

final class CambioParaAvisoLookupTest extends TestCase
{
    public function test_find_devuelve_cambio_public_cuando_no_esta_en_dl(): void
    {
        $cambioPublic = new Cambio();
        $cambioPublic->setId_item_cambio(42);
        $cambioPublic->setId_schema(3000);

        $dlRepository = $this->createMock(CambioDlRepositoryInterface::class);
        $dlRepository->expects($this->once())
            ->method('getCambios')
            ->with(['id_schema' => 3000, 'id_item_cambio' => 42])
            ->willReturn([]);

        $publicRepository = $this->createMock(CambioRepositoryInterface::class);
        $publicRepository->expects($this->once())
            ->method('getCambios')
            ->with(['id_schema' => 3000, 'id_item_cambio' => 42])
            ->willReturn([$cambioPublic]);

        $lookup = new CambioParaAvisoLookup($publicRepository, $dlRepository);

        $this->assertSame($cambioPublic, $lookup->find(3000, 42));
    }

    public function test_find_prefiere_cambio_dl_si_existe(): void
    {
        $cambioDl = new Cambio();
        $cambioDl->setId_item_cambio(7);
        $cambioDl->setId_schema(3001);

        $dlRepository = $this->createMock(CambioDlRepositoryInterface::class);
        $dlRepository->expects($this->once())
            ->method('getCambios')
            ->with(['id_schema' => 3001, 'id_item_cambio' => 7])
            ->willReturn([$cambioDl]);

        $publicRepository = $this->createMock(CambioRepositoryInterface::class);
        $publicRepository->expects($this->never())->method('getCambios');

        $lookup = new CambioParaAvisoLookup($publicRepository, $dlRepository);

        $this->assertSame($cambioDl, $lookup->find(3001, 7));
    }

    public function test_find_devuelve_null_si_no_hay_cambio(): void
    {
        $dlRepository = $this->createMock(CambioDlRepositoryInterface::class);
        $dlRepository->method('getCambios')->willReturn([]);

        $publicRepository = $this->createMock(CambioRepositoryInterface::class);
        $publicRepository->method('getCambios')->willReturn([]);

        $lookup = new CambioParaAvisoLookup($publicRepository, $dlRepository);

        $this->assertNull($lookup->find(3000, 999));
    }
}
