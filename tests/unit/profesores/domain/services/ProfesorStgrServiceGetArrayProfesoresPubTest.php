<?php

declare(strict_types=1);

namespace Tests\unit\profesores\domain\services;

use PHPUnit\Framework\TestCase;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\personas\domain\value_objects\PersonaApellido1Text;
use src\personas\domain\value_objects\PersonaNombreText;
use src\personas\domain\value_objects\SituacionCode;
use src\profesores\domain\services\ProfesorStgrService;

final class ProfesorStgrServiceGetArrayProfesoresPubTest extends TestCase
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

    public function test_ordena_sin_acentos_alvarez_con_las_a(): void
    {
        $personas = [
            $this->personaPub(10, 'Zapata', 'Ana'),
            $this->personaPub(20, 'Álvarez', 'Pedro'),
            $this->personaPub(30, 'Amador', 'Luis'),
        ];

        $personaPubRepo = $this->createMock(PersonaPubRepositoryInterface::class);
        $personaPubRepo->method('getPersonas')->willReturn($personas);

        $GLOBALS['container'] = new class($personaPubRepo) {
            public function __construct(private readonly PersonaPubRepositoryInterface $personaPubRepo) {}

            public function get(string $id): object
            {
                if ($id === PersonaPubRepositoryInterface::class) {
                    return $this->personaPubRepo;
                }
                throw new \RuntimeException('Unexpected DI key: ' . $id);
            }
        };

        $service = new ProfesorStgrService(
            $this->createMock(ProfesorStgrRepositoryInterface::class),
            $this->createMock(\src\personas\domain\contracts\PersonaDlRepositoryInterface::class),
        );

        $opciones = $service->getArrayProfesoresPub();

        // Por apellido1 sin acentos: Álvarez y Amador (A), luego Zapata (Z).
        $this->assertSame(
            ['Álvarez, Pedro', 'Amador, Luis', 'Zapata, Ana'],
            array_values($opciones),
        );
    }

    private function personaPub(int $idNom, string $ap1, string $nom): object
    {
        $persona = $this->createMock(\src\personas\domain\entity\PersonaPub::class);
        $persona->method('getId_nom')->willReturn($idNom);
        $persona->method('getSituacionVo')->willReturn(new SituacionCode('A'));
        $persona->method('getApellido1Vo')->willReturn(new PersonaApellido1Text($ap1));
        $persona->method('getApellido2Vo')->willReturn(null);
        $persona->method('getNomVo')->willReturn(new PersonaNombreText($nom));
        $persona->method('getPrefApellidosNombre')->willReturn($ap1 . ', ' . $nom);

        return $persona;
    }
}
