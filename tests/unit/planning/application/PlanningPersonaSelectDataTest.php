<?php

declare(strict_types=1);

namespace Tests\unit\planning\application;

use PHPUnit\Framework\TestCase;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\planning\application\PlanningPersonaSelectData;

final class PlanningPersonaSelectDataTest extends TestCase
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

    public function test_mapea_filas_del_repositorio(): void
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getId_nom')->willReturn(100);
        $p->method('getId_tabla')->willReturn('n');
        $p->method('getPrefApellidosNombre')->willReturn('Apellido, Nombre');
        $p->method('getCentro_o_dl')->willReturn('Mi centro');

        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->expects($this->once())->method('getPersonas')->willReturn([$p]);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $repo,
            PersonaDlRepositoryInterface::class => $this->createMock(PersonaDlRepositoryInterface::class),
        ]);

        $out = PlanningPersonaSelectData::execute([
            'obj_pau' => 'PersonaN',
            'apellido1' => '',
            'centro' => '',
        ]);

        $this->assertCount(1, $out);
        $this->assertSame(100, $out[0]['id_nom']);
        $this->assertSame('n', $out[0]['id_tabla']);
        $this->assertSame('Apellido, Nombre', $out[0]['pref_apellidos_nombre']);
        $this->assertSame('Mi centro', $out[0]['centro_o_dl']);
    }

    public function test_lista_vacia(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('getPersonas')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $repo,
            PersonaDlRepositoryInterface::class => $this->createMock(PersonaDlRepositoryInterface::class),
        ]);

        $this->assertSame([], PlanningPersonaSelectData::execute(['obj_pau' => 'PersonaN']));
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
