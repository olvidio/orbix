<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use src\ubis\application\DireccionesResolver;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;

final class DireccionesResolverTest extends TestCase
{
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $GLOBALS['container'] = $this->fakeContainer();
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public static function direccionMappingProvider(): array
    {
        return [
            'DireccionCentroDl' => ['DireccionCentroDl', DireccionCentroDlRepositoryInterface::class],
            'DireccionCentroEx' => ['DireccionCentroEx', DireccionCentroExRepositoryInterface::class],
            'DireccionCdcDl' => ['DireccionCdcDl', DireccionCasaDlRepositoryInterface::class],
            'DireccionCdcEx' => ['DireccionCdcEx', DireccionCasaExRepositoryInterface::class],
        ];
    }

    /** @dataProvider direccionMappingProvider */
    #[\PHPUnit\Framework\Attributes\DataProvider('direccionMappingProvider')]
    public function test_direccionRepo_devuelve_repo_segun_objeto(string $objDir, string $expectedInterface): void
    {
        $repo = DireccionesResolver::direccionRepo($objDir);
        $this->assertSame($expectedInterface, $repo->key);
    }

    public static function ubiMappingProvider(): array
    {
        return [
            'DireccionCentroDl' => ['DireccionCentroDl', CentroDlRepositoryInterface::class],
            'DireccionCentroEx' => ['DireccionCentroEx', CentroExRepositoryInterface::class],
            'DireccionCdcDl' => ['DireccionCdcDl', CasaDlRepositoryInterface::class],
            'DireccionCdcEx' => ['DireccionCdcEx', CasaExRepositoryInterface::class],
        ];
    }

    /** @dataProvider ubiMappingProvider */
    #[\PHPUnit\Framework\Attributes\DataProvider('ubiMappingProvider')]
    public function test_ubiRepo_devuelve_repo_segun_objeto(string $objDir, string $expectedInterface): void
    {
        $repo = DireccionesResolver::ubiRepo($objDir);
        $this->assertSame($expectedInterface, $repo->key);
    }

    public function test_direccionRepo_lanza_excepcion_si_desconocido(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('obj_dir desconocido: foo');
        DireccionesResolver::direccionRepo('foo');
    }

    public function test_ubiRepo_lanza_excepcion_si_vacio(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('obj_dir desconocido: ');
        DireccionesResolver::ubiRepo('');
    }

    private function fakeContainer(): object
    {
        return new class {
            public function get(string $key): object
            {
                return new class($key) {
                    public function __construct(public readonly string $key) {}
                };
            }
        };
    }
}
