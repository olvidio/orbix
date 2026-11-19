<?php

declare(strict_types=1);

namespace Tests\unit\ubiscamas\application;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\criteria\HabitacionField;
use src\ubiscamas\domain\entity\Habitacion;
use src\ubiscamas\domain\Select_habitaciones_cdc;

final class SelectHabitacionesCdcTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['oConfig'] = new class {
            public function getAmbito(): string
            {
                return 'dl';
            }
        };
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_getSegmentData_monta_tabla_desde_repositorio(): void
    {
        $hid = Uuid::uuid4()->toString();
        $data = $this->segmento($hid, 3);

        $this->assertSame('select2006', $data['tabla']['id_tabla']);
        $this->assertCount(1, $data['tabla']['valores']);
        $this->assertSame("$hid#12#10", $data['tabla']['valores'][1]['sel']);
        $this->assertSame('Norte', $data['tabla']['valores'][1][1]);
        $this->assertSame(_("NO"), $data['tabla']['valores'][1][7]);
        $this->assertSame(['nuevo' => 1, 'id_ubi' => 12], $data['url_nuevo_spec']['query']);
    }

    public function test_con_permiso_de_escritura_ofrece_modificar_borrar_y_crear(): void
    {
        $data = $this->segmento(Uuid::uuid4()->toString(), 3);

        $this->assertCount(2, $data['tabla']['botones']);
        $this->assertArrayHasKey('ctx_eliminar', $data['tabla']['valores'][1]);
        $this->assertNotNull($data['url_nuevo_spec']);
        $this->assertSame(3, $data['hash']['campos_hidden']['permiso']);
    }

    /**
     * Con solo lectura (2) la tabla se ve, pero sin botones, sin cápsula de borrado y sin «nueva habitación».
     */
    public function test_con_solo_lectura_no_ofrece_ninguna_escritura(): void
    {
        $data = $this->segmento(Uuid::uuid4()->toString(), 2);

        $this->assertCount(1, $data['tabla']['valores']);
        $this->assertSame('Norte', $data['tabla']['valores'][1][1]);
        $this->assertSame([], $data['tabla']['botones']);
        $this->assertArrayNotHasKey('ctx_eliminar', $data['tabla']['valores'][1]);
        $this->assertNull($data['url_nuevo_spec']);
        $this->assertSame(2, $data['hash']['campos_hidden']['permiso']);
    }

    /**
     * @return array<string, mixed>
     */
    private function segmento(string $hid, int $permiso): array
    {
        $hab = $this->createMock(Habitacion::class);
        $hab->method('getId_habitacion')->willReturn($hid);
        $hab->method('getId_ubi')->willReturn(12);
        $hab->method('getOrden')->willReturn(10);
        $hab->method('getNombre')->willReturn('Norte');
        $hab->method('getNumero_camas')->willReturn(2);
        $hab->method('getNumero_camas_vip')->willReturn(1);
        $hab->method('getPlanta')->willReturn('3');
        $hab->method('isSillon')->willReturn(true);
        $hab->method('isAdaptada')->willReturn(false);
        $hab->method('getObservacionesVo')->willReturn(null);
        $hab->method('getTipoLavabo')->willReturn(1);
        $hab->method('isDespacho')->willReturn(false);

        $repo = $this->createMock(HabitacionDlRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('matching')
            ->with($this->equalTo(
                Criteria::create()
                    ->where(Filter::eq(HabitacionField::IdUbi, 12))
                    ->orderBy(HabitacionField::Orden)
                    ->addOrderBy(HabitacionField::Planta)
            ))
            ->willReturn([$hab]);

        $sel = new Select_habitaciones_cdc($repo);
        $sel->setId_pau(12);
        $sel->setPau('cdc');
        $sel->setObj_pau('CasaDl');
        $sel->setId_dossier(3102);
        $sel->setPermiso($permiso);
        $sel->setQueSel('sel2006');

        return $sel->getSegmentData();
    }
}
