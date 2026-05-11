<?php

declare(strict_types=1);

namespace Tests\unit\ubiscamas\application;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use src\ubiscamas\application\HabitacionFormData;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\entity\Habitacion;
use src\ubiscamas\domain\value_objects\HabitacionId;
use src\ubiscamas\domain\value_objects\HabitacionNombre;
use src\ubiscamas\domain\value_objects\HabitacionOrden;
use src\ubiscamas\domain\value_objects\NumeroCamas;
use src\ubiscamas\domain\value_objects\PlantaText;
use src\ubiscamas\domain\value_objects\TipoLavabo;

final class HabitacionFormDataTest extends TestCase
{
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
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

    public function test_nueva_habitacion_orden_inicial_sin_previas(): void
    {
        $habRepo = $this->createMock(HabitacionDlRepositoryInterface::class);
        $habRepo->expects($this->once())
            ->method('getHabitaciones')
            ->with(['id_ubi' => 8, '_ordre' => 'orden DESC', '_limit' => 1])
            ->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap([
            HabitacionDlRepositoryInterface::class => $habRepo,
            CamaDlRepositoryInterface::class => $this->createMock(CamaDlRepositoryInterface::class),
        ]);

        $out = HabitacionFormData::build(['nuevo' => '1', 'id_ubi' => 8]);

        $this->assertSame(10, $out['orden']);
        $this->assertSame(1, $out['numero_camas']);
        $this->assertSame(1, $out['numero_camas_vip']);
        $this->assertSame(8, $out['id_ubi']);
        $this->assertSame('', $out['id_habitacion']);
    }

    public function test_nueva_habitacion_incrementa_orden(): void
    {
        $last = $this->createMock(Habitacion::class);
        $last->method('getOrdenVo')->willReturn(new HabitacionOrden(25));

        $habRepo = $this->createMock(HabitacionDlRepositoryInterface::class);
        $habRepo->method('getHabitaciones')->willReturn([$last]);

        $GLOBALS['container'] = $this->containerFromMap([
            HabitacionDlRepositoryInterface::class => $habRepo,
            CamaDlRepositoryInterface::class => $this->createMock(CamaDlRepositoryInterface::class),
        ]);

        $out = HabitacionFormData::build(['nuevo' => '1', 'id_ubi' => 1]);

        $this->assertSame(35, $out['orden']);
    }

    public function test_edita_habitacion_carga_camas(): void
    {
        $hid = Uuid::uuid4()->toString();
        $habId = new HabitacionId($hid);

        $hab = $this->createMock(Habitacion::class);
        $hab->method('getIdUbiVo')->willReturn(3);
        $hab->method('getOrdenVo')->willReturn(new HabitacionOrden(5));
        $hab->method('getNombreVo')->willReturn(new HabitacionNombre('H1'));
        $hab->method('getNumeroCamasVo')->willReturn(new NumeroCamas(2));
        $hab->method('getNumeroCamasVipVo')->willReturn(new NumeroCamas(1));
        $hab->method('getPlantaVo')->willReturn(new PlantaText('2'));
        $hab->method('isSillon')->willReturn(true);
        $hab->method('isAdaptada')->willReturn(false);
        $hab->method('getObservacionesVo')->willReturn(null);
        $hab->method('isDespacho')->willReturn(false);
        $hab->method('getTipoLavaboVo')->willReturn(new TipoLavabo(2));

        $cama = $this->createMock(Cama::class);
        $cama->method('getIdCama')->willReturn('cama-1');
        $cama->method('getDescripcion')->willReturn('A');
        $cama->method('isLarga')->willReturn(true);
        $cama->method('isVip')->willReturn(false);

        $habRepo = $this->createMock(HabitacionDlRepositoryInterface::class);
        $habRepo->expects($this->once())->method('findById')->with($hid)->willReturn($hab);

        $camaRepo = $this->createMock(CamaDlRepositoryInterface::class);
        $camaRepo->expects($this->once())
            ->method('getCamasByHabitacion')
            ->with($this->callback(fn ($id) => $id instanceof HabitacionId && $id->value() === $hid))
            ->willReturn([$cama]);

        $GLOBALS['container'] = $this->containerFromMap([
            HabitacionDlRepositoryInterface::class => $habRepo,
            CamaDlRepositoryInterface::class => $camaRepo,
        ]);

        $out = HabitacionFormData::build([
            'nuevo' => '',
            'sel' => [$hid . '#extra'],
            'id_ubi' => 0,
        ]);

        $this->assertSame($hid, $out['id_habitacion']);
        $this->assertSame(3, $out['id_ubi']);
        $this->assertSame('H1', $out['nombre']);
        $this->assertTrue($out['sillon']);
        $this->assertSame(2, $out['tipoLavabo']);
        $this->assertCount(1, $out['a_camas']);
        $this->assertSame('cama-1', $out['a_camas'][0]['id_cama']);
        $this->assertTrue($out['a_camas'][0]['larga']);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }

                return $this->services[$id];
            }
        };
    }
}
