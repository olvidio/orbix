<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\misas\application\CuadriculaUpdate;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\value_objects\PlantillaConfig;
use src\shared\domain\value_objects\DateTimeLocal;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\ZonaSacd;

/**
 * Al cambiar una celda de la cuadrícula ({@see CuadriculaUpdate}), la fila del sacd se recalcula igual
 * que en la cuadrícula completa: con una actividad, el primer día aún está y los demás no («Está en …»);
 * con una ausencia (encargos 7xxx/4xxx), no está en todo el día. Antes siempre decía «SI».
 */
final class CuadriculaUpdateDisponibilidadTest extends TestCase
{
    private const DIA = '2026-08-10';
    private const SACD = 10011;

    /** @return array<string, mixed> */
    private function ejecutar(?ActividadAll $actividad, ?EncargoSacdHorario $ausencia = null, int $tipoAusencia = 7100): array
    {
        $dias = $this->createMock(EncargoDiaRepositoryInterface::class);
        $dias->method('findById')->willReturn(null);
        $dias->method('matching')->willReturn([]);
        $dias->method('Guardar')->willReturn(true);
        $zona = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zona->method('matching')->willReturnCallback(static function (): array {
            $z = new ZonaSacd();
            $z->setId_nom(self::SACD);
            for ($d = 1; $d <= 7; $d++) {
                $z->{"setDw$d"}(true);
            }
            return [$z];
        });
        $cargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $cargos->method('getAsistenteCargoDeActividad')->willReturn($actividad === null ? [] : [['id_activ' => 3001, 'propio' => true, 'id_cargo' => null]]);
        $actividades = $this->createMock(ActividadRepositoryInterface::class);
        $actividades->method('matching')->willReturn($actividad === null ? [] : [$actividad]);
        $horarios = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $horarios->method('matching')->willReturn($ausencia === null ? [] : [$ausencia]);
        $encargos = $this->createMock(EncargoRepositoryInterface::class);
        $encargos->method('findById')->willReturnCallback(static function (int $id) use ($tipoAusencia): Encargo {
            $e = new Encargo();
            $e->setId_enc($id);
            $e->setId_tipo_enc($tipoAusencia);
            $e->setDesc_enc('Retiro');
            return $e;
        });

        $uc = new CuadriculaUpdate($dias, $zona, $actividades, $cargos, $encargos, $horarios, $this->createStub(InicialesSacdService::class));

        return $uc->execute('11111111-1111-4111-8111-111111111111', 'AB#' . self::SACD, '08:00', '08:30', '', 42, self::DIA, PlantillaConfig::PLAN_DE_MISAS, 3)['meta'];
    }

    private static function actividad(string $ini, string $fin): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ(3001);
        $a->setNom_activ('Convivencia');
        $a->setF_ini(new DateTimeLocal($ini));
        $a->setF_fin(new DateTimeLocal($fin));
        return $a;
    }

    /** @return iterable<string, array{string, string, string}> */
    public static function actividades(): iterable
    {
        yield 'empieza ese día' => ['2026-08-10', '2026-08-12', 'SI'];
        yield 'día intermedio' => ['2026-08-08', '2026-08-12', '--'];
        yield 'termina ese día' => ['2026-08-08', '2026-08-10', '--'];
    }

    #[DataProvider('actividades')]
    public function test_con_una_actividad(string $ini, string $fin, string $esperado): void
    {
        $meta = $this->ejecutar(self::actividad($ini, $fin));

        $this->assertSame($esperado, $meta['texto_sacd']);
        if ($esperado === '--') {
            $this->assertSame('Está en Convivencia', $meta['texto']);
        }
    }

    public function test_sin_actividad_ni_ausencia_esta_en_la_zona(): void
    {
        $meta = $this->ejecutar(null);

        $this->assertSame('SI', $meta['texto_sacd']);
        $this->assertSame('verde', $meta['color_fondo']);
    }

    public function test_una_ausencia_ocupa_el_dia_entero_y_otros_encargos_no_cuentan(): void
    {
        $a = new EncargoSacdHorario();
        $a->setId_enc(700);
        $a->setId_nom(self::SACD);
        $a->setF_ini(new DateTimeLocal(self::DIA));
        $a->setF_fin(new DateTimeLocal(self::DIA));

        $meta = $this->ejecutar(null, $a);
        $this->assertSame('--', $meta['texto_sacd']);
        $this->assertStringStartsWith('Está en Retiro (', $meta['texto']);

        $this->assertSame('SI', $this->ejecutar(null, $a, 8110)['texto_sacd'], 'un encargo de misa no es una ausencia');
    }
}
