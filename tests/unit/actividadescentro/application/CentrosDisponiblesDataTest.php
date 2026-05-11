<?php

declare(strict_types=1);

namespace Tests\unit\actividadescentro\application;

use PHPUnit\Framework\TestCase;
use src\actividadescentro\application\CentrosDisponiblesData;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

final class CentrosDisponiblesDataTest extends TestCase
{
    private mixed $previousContainer = null;
    private bool $hadIdioma = false;
    private mixed $previousIdioma = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
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
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_tipo_invalido_devuelve_error(): void
    {
        $out = CentrosDisponiblesData::execute(['tipo' => 'xyz', 'id_activ' => 1]);

        $this->assertSame('xyz', $out['tipo']);
        $this->assertSame(1, $out['id_activ']);
        $this->assertSame([], $out['centros']);
        $this->assertNotSame('', (string) $out['error']);
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

        $GLOBALS['container'] = new class($dl) {
            public function __construct(private readonly CentroDlRepositoryInterface $dl) {}

            public function get(string $key): object
            {
                if ($key !== CentroDlRepositoryInterface::class) {
                    throw new \RuntimeException('Clave inesperada: ' . $key);
                }

                return $this->dl;
            }
        };

        $out = CentrosDisponiblesData::execute(['tipo' => 'sr', 'id_activ' => 5]);

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

        $GLOBALS['container'] = new class($dl, $enc) {
            public function __construct(
                private readonly CentroDlRepositoryInterface $dl,
                private readonly CentroEncargadoRepositoryInterface $enc,
            ) {}

            public function get(string $key): object
            {
                return match ($key) {
                    CentroDlRepositoryInterface::class => $this->dl,
                    CentroEncargadoRepositoryInterface::class => $this->enc,
                    default => throw new \RuntimeException('Clave inesperada: ' . $key),
                };
            }
        };

        $out = CentrosDisponiblesData::execute([
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
