<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\support\HuecoOpcionalDePlan;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;

final class HuecoOpcionalDePlanTest extends TestCase
{
    /** @var list<int> */
    private array $slots2026 = [2430, 2431, 2432, 2433, 2434];

    public function test_fin_de_bienio_no_es_opcional(): void
    {
        $this->assertTrue(HuecoOpcionalDePlan::esMarcadorFinCiclo(9999));
        $this->assertTrue(HuecoOpcionalDePlan::esMarcadorFinCiclo(9998));
        $this->assertFalse(HuecoOpcionalDePlan::esOpcionalConcreta(9999));
        $this->assertFalse(HuecoOpcionalDePlan::esOpcionalConcreta(9998));
        $this->assertTrue(HuecoOpcionalDePlan::esOpcionalConcreta(3411));
    }

    public function test_rellena_el_primer_hueco_aunque_no_sea_consecutivo(): void
    {
        $notas = [
            ['id_asignatura' => 3411, 'id_nivel' => 2430],
            ['id_asignatura' => 3502, 'id_nivel' => 2432],
        ];

        $this->assertSame(
            2431,
            HuecoOpcionalDePlan::resolver(3601, $this->slots2026, $notas),
        );
    }

    public function test_un_nivel_fuera_del_plan_no_ocupa_los_huecos_validos(): void
    {
        $notas = [
            ['id_asignatura' => 3411, 'id_nivel' => 1230],
        ];

        $this->assertSame(
            2430,
            HuecoOpcionalDePlan::resolver(3601, $this->slots2026, $notas),
        );
    }

    public function test_una_nota_no_aprobada_tambien_ocupa_el_hueco(): void
    {
        $notas = [
            ['id_asignatura' => 3411, 'id_nivel' => 2430],
        ];

        $this->assertSame(
            2431,
            HuecoOpcionalDePlan::resolver(3601, $this->slots2026, $notas),
        );
    }

    public function test_conserva_el_hueco_si_la_asignatura_ya_esta_en_uno_valido(): void
    {
        $notas = [
            ['id_asignatura' => 3411, 'id_nivel' => 2430],
            ['id_asignatura' => 3601, 'id_nivel' => 2432],
        ];

        $this->assertSame(
            2432,
            HuecoOpcionalDePlan::resolver(3601, $this->slots2026, $notas),
        );
    }

    public function test_si_el_hueco_guardado_no_es_del_plan_busca_uno_libre(): void
    {
        $notas = [
            ['id_asignatura' => 3411, 'id_nivel' => 2430],
            ['id_asignatura' => 3601, 'id_nivel' => 1231],
        ];

        $this->assertSame(
            2431,
            HuecoOpcionalDePlan::resolver(3601, $this->slots2026, $notas),
        );
    }

    public function test_fin_bienio_en_9999_no_tapa_un_hueco(): void
    {
        $notas = [
            ['id_asignatura' => 9999, 'id_nivel' => 9999],
        ];

        $this->assertSame(
            2430,
            HuecoOpcionalDePlan::resolver(3411, $this->slots2026, $notas),
        );
    }

    public function test_fin_bienio_aparcado_en_un_slot_si_lo_ocupa(): void
    {
        $notas = [
            ['id_asignatura' => 9999, 'id_nivel' => 2430],
        ];

        $this->assertSame(
            2431,
            HuecoOpcionalDePlan::resolver(3411, $this->slots2026, $notas),
        );
    }

    public function test_sin_hueco_libre_devuelve_null(): void
    {
        $notas = [];
        foreach ($this->slots2026 as $i => $nivel) {
            $notas[] = ['id_asignatura' => 3400 + $i, 'id_nivel' => $nivel];
        }

        $this->assertNull(HuecoOpcionalDePlan::resolver(3601, $this->slots2026, $notas));
    }

    public function test_plan_1997_puede_usar_el_hueco_de_bienio(): void
    {
        $slots = [1230, 1231, 1232, 2430, 2431, 2432, 2433, 2434];

        $this->assertSame(1230, HuecoOpcionalDePlan::resolver(3411, $slots, []));
    }

    public function test_niveles_del_plan_solo_opcionales_genericas_ordenadas(): void
    {
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->method('getAsignaturas')->willReturnCallback(
            function (array $where, array $operador): array {
                $this->assertSame(HuecoOpcionalDePlan::ID_TIPO_OPCIONAL, $where['id_tipo']);
                $this->assertSame(PlanEstudios::PLAN_2026, $where['plan_estudios']);
                $this->assertSame('<', $operador['id_nivel']);
                $this->assertSame('<', $operador['id_asignatura']);

                return [
                    $this->asignatura(2432, 2432, 8),
                    $this->asignatura(2430, 2430, 8),
                    $this->asignatura(3411, 3411, 8),
                    $this->asignatura(2434, 2434, 8),
                    $this->asignatura(1230, 1230, 8, false),
                ];
            }
        );

        $hueco = new HuecoOpcionalDePlan(
            $repo,
            new PlanEstudiosDePersona($this->createMock(PersonaNotaRepositoryInterface::class)),
        );

        $this->assertSame(
            [2430, 2432, 2434],
            $hueco->nivelesDelPlan(PlanEstudios::PLAN_2026),
        );
    }

    private function asignatura(int $id, int $nivel, int $tipo, bool $active = true): Asignatura
    {
        $asig = $this->createMock(Asignatura::class);
        $asig->method('getId_asignatura')->willReturn($id);
        $asig->method('getId_nivel')->willReturn($nivel);
        $asig->method('getId_tipo')->willReturn($tipo);
        $asig->method('isActive')->willReturn($active);

        return $asig;
    }
}
