<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use src\ubis\application\DireccionesResolver;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;

final class DireccionesResolverTest extends TestCase
{
    private DireccionesResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new DireccionesResolver(
            $this->createMock(DireccionCentroRepositoryInterface::class),
            $this->createMock(DireccionCentroDlRepositoryInterface::class),
            $this->createMock(DireccionCentroExRepositoryInterface::class),
            $this->createMock(DireccionCasaRepositoryInterface::class),
            $this->createMock(DireccionCasaDlRepositoryInterface::class),
            $this->createMock(DireccionCasaExRepositoryInterface::class),
            $this->createMock(CentroRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(CentroExRepositoryInterface::class),
            $this->createMock(CasaRepositoryInterface::class),
            $this->createMock(CasaDlRepositoryInterface::class),
            $this->createMock(CasaExRepositoryInterface::class),
        );
    }

    public static function direccionMappingProvider(): array
    {
        return [
            'DireccionCentro' => ['DireccionCentro', DireccionCentroRepositoryInterface::class],
            'DireccionCentroDl' => ['DireccionCentroDl', DireccionCentroDlRepositoryInterface::class],
            'DireccionCentroEx' => ['DireccionCentroEx', DireccionCentroExRepositoryInterface::class],
            'DireccionCdc' => ['DireccionCdc', DireccionCasaRepositoryInterface::class],
            'DireccionCdcDl' => ['DireccionCdcDl', DireccionCasaDlRepositoryInterface::class],
            'DireccionCdcEx' => ['DireccionCdcEx', DireccionCasaExRepositoryInterface::class],
        ];
    }

    /** @dataProvider direccionMappingProvider */
    #[\PHPUnit\Framework\Attributes\DataProvider('direccionMappingProvider')]
    public function test_direccionRepo_devuelve_repo_segun_objeto(string $objDir, string $expectedInterface): void
    {
        $repo = $this->resolver->direccionRepo($objDir);
        $this->assertInstanceOf($expectedInterface, $repo);
    }

    public static function ubiMappingProvider(): array
    {
        return [
            'DireccionCentro' => ['DireccionCentro', CentroRepositoryInterface::class],
            'DireccionCentroDl' => ['DireccionCentroDl', CentroDlRepositoryInterface::class],
            'DireccionCentroEx' => ['DireccionCentroEx', CentroExRepositoryInterface::class],
            'DireccionCdc' => ['DireccionCdc', CasaRepositoryInterface::class],
            'DireccionCdcDl' => ['DireccionCdcDl', CasaDlRepositoryInterface::class],
            'DireccionCdcEx' => ['DireccionCdcEx', CasaExRepositoryInterface::class],
        ];
    }

    /** @dataProvider ubiMappingProvider */
    #[\PHPUnit\Framework\Attributes\DataProvider('ubiMappingProvider')]
    public function test_ubiRepo_devuelve_repo_segun_objeto(string $objDir, string $expectedInterface): void
    {
        $repo = $this->resolver->ubiRepo($objDir);
        $this->assertInstanceOf($expectedInterface, $repo);
    }

    public function test_direccionRepo_lanza_excepcion_si_desconocido(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('obj_dir desconocido: foo');
        $this->resolver->direccionRepo('foo');
    }

    public function test_ubiRepo_lanza_excepcion_si_vacio(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('obj_dir desconocido: ');
        $this->resolver->ubiRepo('');
    }
}
