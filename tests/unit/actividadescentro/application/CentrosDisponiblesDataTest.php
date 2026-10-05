<?php

declare(strict_types=1);

namespace Tests\unit\actividadescentro\application;

use PHPUnit\Framework\TestCase;
use src\actividadescentro\application\CentrosDisponiblesData;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

final class CentrosDisponiblesDataTest extends TestCase
{
    private bool $hadIdioma = false;
    private mixed $previousIdioma = null;

    protected function setUp(): void
    {
        parent::setUp();
        if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
            $_SESSION['session_auth'] = [];
        }
        $this->hadIdioma = array_key_exists('idioma', $_SESSION['session_auth']);
        $this->previousIdioma = $this->hadIdioma ? $_SESSION['session_auth']['idioma'] : null;
        $_SESSION['session_auth']['idioma'] = 'es_ES.UTF-8';
    }

    protected function tearDown(): void
    {
        if ($this->hadIdioma) {
            $_SESSION['session_auth']['idioma'] = $this->previousIdioma;
        } else {
            unset($_SESSION['session_auth']['idioma']);
        }
        parent::tearDown();
    }

    /**
     * @dataProvider provider_tipo_desde_id_tipo_activ
     */
    public function test_tipo_desde_id_tipo_activ(string $id_tipo_activ, ?string $esperado): void
    {
        $this->assertSame($esperado, CentrosDisponiblesData::tipoDesdeIdTipoActiv($id_tipo_activ));
    }

    /**
     * @return iterable<string, array{0: string, 1: ?string}>
     */
    public static function provider_tipo_desde_id_tipo_activ(): iterable
    {
        yield 'sg sv' => ['141000', 'sg'];
        yield 'sr sv' => ['170000', 'sr'];
        yield 'nagd sv' => ['110000', 'nagd'];
        yield 'sssc' => ['160000', 'sssc'];
        yield 'sfsg' => ['241000', 'sfsg'];
        yield 'sfsr' => ['270000', 'sfsr'];
        yield 'sfnagd' => ['210000', 'sfnagd'];
        yield 'desconocido' => ['990000', null];
        yield 'vacio' => ['', null];
    }

    public function test_tipo_invalido_devuelve_error(): void
    {
        $useCase = new CentrosDisponiblesData(
            $this->createStub(CentroEncargadoRepositoryInterface::class),
            $this->createStub(CentroDlRepositoryInterface::class),
            $this->createStub(CentroEllasRepositoryInterface::class),
        );

        $out = $useCase->execute(['tipo' => 'xyz', 'id_activ' => 1]);

        $this->assertSame('xyz', $out['tipo']);
        $this->assertSame(1, $out['id_activ']);
        $this->assertSame([], $out['centros']);
        $this->assertNotSame('', (string) $out['error']);
    }

    public function test_resuelve_tipo_desde_id_tipo_activ_cuando_tipo_vacio(): void
    {
        $c = new class {
            public function getId_ubi(): int
            {
                return 2;
            }
            public function getNombre_ubi(): string
            {
                return 'DL2';
            }
        };

        $ellas = $this->createStub(CentroEllasRepositoryInterface::class);
        $ellas->method('getCentros')->willReturn([$c]);

        $useCase = new CentrosDisponiblesData(
            $this->createStub(CentroEncargadoRepositoryInterface::class),
            $this->createStub(CentroDlRepositoryInterface::class),
            $ellas,
        );

        $out = $useCase->execute([
            'tipo' => '',
            'id_activ' => 5,
            'id_tipo_activ' => '270000',
        ]);

        $this->assertSame('sfsr', $out['tipo']);
        $this->assertArrayNotHasKey('error', $out);
        $this->assertSame([['id_ubi' => 2, 'nombre_ubi' => 'DL2']], $out['centros']);
    }

    public function test_sr_mapea_centros_desde_dl(): void
    {
        $c = new class {
            public function getId_ubi(): int
            {
                return 2;
            }
            public function getNombre_ubi(): string
            {
                return 'DL2';
            }
        };

        $dl = $this->createStub(CentroDlRepositoryInterface::class);
        $dl->method('getCentros')->willReturn([$c]);

        $useCase = new CentrosDisponiblesData(
            $this->createStub(CentroEncargadoRepositoryInterface::class),
            $dl,
            $this->createStub(CentroEllasRepositoryInterface::class),
        );

        $out = $useCase->execute(['tipo' => 'sr', 'id_activ' => 5]);

        $this->assertArrayNotHasKey('error', $out);
        $this->assertSame([['id_ubi' => 2, 'nombre_ubi' => 'DL2']], $out['centros']);
    }

    public function test_sg_incluye_conteos_y_dif_cuando_hay_datos(): void
    {
        $centro = new class {
            public function getId_ubi(): int
            {
                return 8;
            }
            public function getNombre_ubi(): string
            {
                return 'Sede';
            }
        };

        $dl = $this->createStub(CentroDlRepositoryInterface::class);
        $dl->method('getCentros')->willReturn([$centro]);

        $enc = $this->createStub(CentroEncargadoRepositoryInterface::class);
        $enc->method('getActividadesDeCentros')->with(8, "f_ini BETWEEN '2024-01-01' AND '2024-12-31'")->willReturn([1, 2, 3]);
        $enc->method('getProximasActividadesDeCentro')->with(8, '2024-06-15')->willReturn('5');

        $useCase = new CentrosDisponiblesData(
            $enc,
            $dl,
            $this->createStub(CentroEllasRepositoryInterface::class),
        );

        $out = $useCase->execute([
            'tipo' => 'sg',
            'id_activ' => 1,
            'inicio' => '2024-01-01',
            'fin' => '2024-12-31',
            'f_ini_act' => '15/06/2024',
        ]);

        $this->assertSame([
            [
                'id_ubi' => 8,
                'nombre_ubi' => 'Sede',
                'num_actividades_periodo' => 3,
                'dif_dias' => '5',
            ],
        ], $out['centros']);
    }
}
