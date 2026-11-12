<?php

declare(strict_types=1);

namespace Tests\unit\actividadescentro\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\actividadescentro\application\CentrosDisponiblesData;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

/**
 * El periodo de los centros de sg (inicio/fin del request) se pasa al
 * repositorio como condición SQL: solo se admiten fechas ISO válidas.
 */
final class CentrosDisponiblesPeriodoTest extends TestCase
{
    private mixed $sesion = null;

    protected function setUp(): void
    {
        $this->sesion = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv', 'idioma' => 'es_ES.UTF-8'];
    }

    protected function tearDown(): void
    {
        if ($this->sesion === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesion;
        }
    }

    /** @return list<string> condiciones recibidas por el repositorio */
    private function condiciones(string $inicio, string $fin): array
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
        $dl->method('matching')->willReturn([$centro]);
        $vistas = [];
        $enc = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $enc->method('getActividadesDeCentros')->willReturnCallback(function (int $id, string $cond) use (&$vistas): array {
            $vistas[] = $cond;
            return [];
        });
        (new CentrosDisponiblesData($enc, $dl, $this->createStub(CentroEllasRepositoryInterface::class)))
            ->execute(['tipo' => 'sg', 'id_activ' => 1, 'inicio' => $inicio, 'fin' => $fin]);
        return $vistas;
    }

    public function test_fechas_iso_validas_se_usan(): void
    {
        $this->assertSame(["f_ini BETWEEN '2027-01-01' AND '2027-12-31'"], $this->condiciones('2027-01-01', '2027-12-31'));
    }

    public function test_fechas_con_barras_de_periodo_se_normalizan(): void
    {
        // Periodo devuelve «desde hoy» y otros periodos como Y/m/d.
        $this->assertSame(["f_ini BETWEEN '2027-01-01' AND '2027-06-30'"], $this->condiciones('2027/01/01', '2027/06/30'));
    }

    public static function providerNoValidas(): \Generator
    {
        yield 'inyección en inicio' => ["2027-01-01' OR '1'='1", '2027-12-31'];
        yield 'inyección en fin' => ['2027-01-01', "2027-12-31') UNION SELECT id_activ FROM a_actividades_dl --"];
        yield 'formato local' => ['1/1/2027', '31/12/2027'];
        yield 'fecha imposible' => ['2027-02-30', '2027-12-31'];
    }

    #[DataProvider('providerNoValidas')]
    public function test_fechas_no_validas_no_llegan_al_sql(string $inicio, string $fin): void
    {
        $this->assertSame([], $this->condiciones($inicio, $fin));
    }
}
