<?php

declare(strict_types=1);

namespace Tests\unit\ubiscamas\application;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\entity\Habitacion;
use src\ubiscamas\domain\SelectHabitacionesCdc;
final class SelectHabitacionesCdcTest extends TestCase
{
    private mixed $previousContainer;

    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
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
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_getSegmentData_monta_tabla_desde_repositorio(): void
    {
        $hid = Uuid::uuid4()->toString();

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
            ->method('getHabitaciones')
            ->with(['id_ubi' => 12, '_ordre' => 'orden, planta'])
            ->willReturn([$hab]);

        $GLOBALS['container'] = $this->containerFromMap([
            HabitacionDlRepositoryInterface::class => $repo,
        ]);

        $sel = new SelectHabitacionesCdc();
        $sel->setId_pau(12);
        $sel->setPau('cdc');
        $sel->setObj_pau('CasaDl');
        $sel->setId_dossier(3102);
        $sel->setPermiso(3);
        $sel->setQueSel('sel2006');

        $data = $sel->getSegmentData();

        $this->assertSame('select2006', $data['tabla']['id_tabla']);
        $this->assertCount(1, $data['tabla']['valores']);
        $this->assertSame("$hid#12#10", $data['tabla']['valores'][1]['sel']);
        $this->assertSame('Norte', $data['tabla']['valores'][1][1]);
        $this->assertSame(_('NO'), $data['tabla']['valores'][1][7]);
        $this->assertSame(['nuevo' => 1, 'id_ubi' => 12], $data['url_nuevo_spec']['query']);
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
