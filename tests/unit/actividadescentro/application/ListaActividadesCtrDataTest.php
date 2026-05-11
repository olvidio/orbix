<?php

declare(strict_types=1);

namespace Tests\unit\actividadescentro\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividadescentro\application\ListaActividadesCtrData;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaRepositoryInterface;

final class ListaActividadesCtrDataTest extends TestCase
{
    private mixed $previousContainer = null;
    private mixed $previousPermActividades = null;
    private bool $hadIdioma = false;
    private mixed $previousIdioma = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousPermActividades = $_SESSION['oPermActividades'] ?? null;
        unset($_SESSION['oPermActividades']);
        if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
            $_SESSION['session_auth'] = [];
        }
        $_SESSION['session_auth']['id_usuario'] = $_SESSION['session_auth']['id_usuario'] ?? 1;
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
        if ($this->previousPermActividades === null) {
            unset($_SESSION['oPermActividades']);
        } else {
            $_SESSION['oPermActividades'] = $this->previousPermActividades;
        }
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_sin_actividades_contrato_y_filas_vacias(): void
    {
        $act = $this->createStub(ActividadDlRepositoryInterface::class);
        $act->method('getActividades')->willReturn([]);
        $ce = $this->createStub(CentroEncargadoRepositoryInterface::class);
        $casa = $this->createStub(CasaRepositoryInterface::class);
        $casa->method('getArrayCasas')->willReturn([]);

        $GLOBALS['container'] = $this->containerTres($act, $ce, $casa);

        $out = ListaActividadesCtrData::execute([
            'tipo' => 'sg',
            'year' => '',
            'periodo' => 'actual',
        ]);

        $this->assertSame(['titulo', 'tipo', 'inicio_iso', 'fin_iso', 'filas'], array_keys($out));
        $this->assertSame('sg', $out['tipo']);
        $this->assertSame([], $out['filas']);
        $this->assertIsString($out['inicio_iso']);
        $this->assertIsString($out['fin_iso']);
    }

    public function test_una_actividad_con_centro_encargado(): void
    {
        $fIni = new DateTimeLocal('2024-05-01');
        $fFin = new DateTimeLocal('2024-05-02');
        $activ = new class($fIni, $fFin) {
            public function __construct(
                private readonly DateTimeLocal $fIni,
                private readonly DateTimeLocal $fFin,
            ) {}

            public function getId_activ(): int
            {
                return 10;
            }
            public function getId_tipo_activ(): int
            {
                return 144000;
            }
            public function getDl_org(): ?string
            {
                return 'u';
            }
            public function getNom_activ(): string
            {
                return 'Act test';
            }
            public function getF_ini(): ?DateTimeLocal
            {
                return $this->fIni;
            }
            public function getF_fin(): ?DateTimeLocal
            {
                return $this->fFin;
            }
            public function getId_ubi(): ?int
            {
                return 2;
            }
        };

        $centroEnc = new class {
            public function getId_ubi(): int
            {
                return 99;
            }
            public function getNombre_ubi(): string
            {
                return 'Enc';
            }
        };

        $act = $this->createStub(ActividadDlRepositoryInterface::class);
        $act->method('getActividades')->willReturn([$activ]);

        $ce = $this->createStub(CentroEncargadoRepositoryInterface::class);
        $ce->method('getCentrosEncargadosActividad')->with(10)->willReturn([$centroEnc]);

        $casa = $this->createStub(CasaRepositoryInterface::class);
        $casa->method('getArrayCasas')->willReturn([2 => 'Casa dos']);

        $GLOBALS['container'] = $this->containerTres($act, $ce, $casa);

        $out = ListaActividadesCtrData::execute([
            'tipo' => 'sg',
            'periodo' => 'actual',
        ]);

        $this->assertCount(1, $out['filas']);
        $row = $out['filas'][0];
        $this->assertSame(10, $row['id_activ']);
        $this->assertSame('Act test', $row['nom_activ']);
        $this->assertTrue($row['perm_modificar_ctr']);
        $this->assertTrue($row['perm_crear_ctr']);
        $this->assertSame([['id_ubi' => 99, 'nombre_ubi' => 'Enc']], $row['centros']);
    }

    private function containerTres(
        ActividadDlRepositoryInterface $a,
        CentroEncargadoRepositoryInterface $c,
        CasaRepositoryInterface $s,
    ): object {
        return new class($a, $c, $s) {
            public function __construct(
                private readonly ActividadDlRepositoryInterface $a,
                private readonly CentroEncargadoRepositoryInterface $c,
                private readonly CasaRepositoryInterface $s,
            ) {}

            public function get(string $key): object
            {
                return match ($key) {
                    ActividadDlRepositoryInterface::class => $this->a,
                    CentroEncargadoRepositoryInterface::class => $this->c,
                    CasaRepositoryInterface::class => $this->s,
                    default => throw new \RuntimeException('Clave inesperada: ' . $key),
                };
            }
        };
    }
}
