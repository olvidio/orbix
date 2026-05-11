<?php

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\ProfesoresDesplegableData;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\profesores\domain\services\ProfesorAsignaturaService;
use src\profesores\domain\services\ProfesorStgrService;

final class ProfesoresDesplegableDataTest extends TestCase
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

    public function test_salida_desconocida_devuelve_opciones_vacias(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([]);

        $out = ProfesoresDesplegableData::execute(['salida' => 'otro']);
        $this->assertSame([
            'id' => 'id_profesor',
            'opciones' => [],
            'blanco' => true,
            'val_blanco' => '',
            'selected' => -1,
        ], $out);
    }

    public function test_salida_asignatura_usa_profesor_asignatura_service(): void
    {
        $opciones = [5 => 'Prof. X'];
        $svc = $this->createMock(ProfesorAsignaturaService::class);
        $svc->expects($this->once())
            ->method('getArrayTodosProfesoresAsignatura')
            ->with($this->callback(fn (AsignaturaId $id) => $id->value() === 1000))
            ->willReturn($opciones);

        $GLOBALS['container'] = $this->containerFromMap([
            ProfesorAsignaturaService::class => $svc,
        ]);

        $out = ProfesoresDesplegableData::execute([
            'salida' => 'asignatura',
            'id_asignatura' => 1000,
        ]);
        $this->assertSame($opciones, $out['opciones']);
    }

    public function test_salida_todos_usa_profesor_stgr_service(): void
    {
        $opciones = [1 => 'Pub 1'];
        $svc = $this->createMock(ProfesorStgrService::class);
        $svc->expects($this->once())->method('getArrayProfesoresPub')->willReturn($opciones);

        $GLOBALS['container'] = $this->containerFromMap([
            ProfesorStgrService::class => $svc,
        ]);

        $out = ProfesoresDesplegableData::execute(['salida' => 'todos']);
        $this->assertSame($opciones, $out['opciones']);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class($services) {
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
