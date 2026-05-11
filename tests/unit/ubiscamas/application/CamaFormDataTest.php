<?php

declare(strict_types=1);

namespace Tests\unit\ubiscamas\application;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use src\ubiscamas\application\CamaFormData;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\value_objects\CamaId;
use src\ubiscamas\domain\value_objects\HabitacionId;

final class CamaFormDataTest extends TestCase
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

    public function test_sin_id_cama_genera_uuid_y_no_consulta_repo(): void
    {
        $repo = $this->createMock(CamaDlRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');

        $GLOBALS['container'] = $this->containerFromMap([
            CamaDlRepositoryInterface::class => $repo,
        ]);

        $out = CamaFormData::build(['id_ubi' => 4, 'id_habitacion' => 'hab-x', 'id_cama' => '']);

        $this->assertNotSame('', $out['id_cama']);
        $this->assertTrue(Uuid::isValid($out['id_cama']));
        $this->assertSame('', $out['descripcion']);
        $this->assertFalse($out['larga']);
        $this->assertFalse($out['vip']);
        $this->assertSame(4, $out['id_ubi']);
    }

    public function test_con_id_cama_carga_desde_repositorio(): void
    {
        $cid = Uuid::uuid4()->toString();
        $hid = Uuid::uuid4()->toString();

        $cama = $this->createMock(Cama::class);
        $cama->method('getIdHabitacionVo')->willReturn(new HabitacionId($hid));
        $cama->method('getDescripcion')->willReturn('Lateral');
        $cama->method('isLarga')->willReturn(true);
        $cama->method('isVip')->willReturn(true);

        $repo = $this->createMock(CamaDlRepositoryInterface::class);
        $repo->expects($this->once())->method('findById')->with($cid)->willReturn($cama);

        $GLOBALS['container'] = $this->containerFromMap([
            CamaDlRepositoryInterface::class => $repo,
        ]);

        $out = CamaFormData::build([
            'id_cama' => $cid,
            'id_ubi' => 2,
            'mod' => 'edit',
        ]);

        $this->assertSame($cid, $out['id_cama']);
        $this->assertSame($hid, $out['id_habitacion']);
        $this->assertSame('Lateral', $out['descripcion']);
        $this->assertTrue($out['larga']);
        $this->assertTrue($out['vip']);
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
