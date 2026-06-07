<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\DireccionUpdate;
use src\ubis\application\DireccionesResolver;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;

final class DireccionUpdateTest extends TestCase
{
    private DireccionUpdate $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $resolver = new DireccionesResolver(
            $this->createMock(DireccionCentroDlRepositoryInterface::class),
            $this->createMock(DireccionCentroExRepositoryInterface::class),
            $this->createMock(DireccionCasaDlRepositoryInterface::class),
            $this->createMock(DireccionCasaExRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(CentroExRepositoryInterface::class),
            $this->createMock(CasaDlRepositoryInterface::class),
            $this->createMock(CasaExRepositoryInterface::class),
        );
        $this->useCase = new DireccionUpdate($resolver);
    }

    public function test_obj_dir_vacio_devuelve_mensaje_de_obj_desconocido(): void
    {
        $msg = $this->useCase->execute([]);
        $this->assertStringContainsString('obj_dir desconocido', $msg);
    }

    public function test_obj_dir_desconocido_devuelve_mensaje_de_obj_desconocido(): void
    {
        $msg = $this->useCase->execute(['obj_dir' => 'NoExiste', 'id_ubi' => 1]);
        $this->assertSame('obj_dir desconocido: NoExiste', $msg);
    }
}
