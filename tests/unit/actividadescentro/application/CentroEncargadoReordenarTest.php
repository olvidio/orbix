<?php

declare(strict_types=1);

namespace Tests\unit\actividadescentro\application;

use PHPUnit\Framework\TestCase;
use src\actividadescentro\application\CentroEncargadoReordenar;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;

final class CentroEncargadoReordenarTest extends TestCase
{
    private mixed $previousContainer = null;

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

    public function test_parametros_o_direccion_invalidos(): void
    {
        $this->assertNotSame('', CentroEncargadoReordenar::execute(['id_activ' => 0, 'id_ubi' => 1, 'num_orden' => 'mas']));
        $this->assertNotSame('', CentroEncargadoReordenar::execute(['id_activ' => 1, 'id_ubi' => 0, 'num_orden' => 'mas']));
        $this->assertNotSame('', CentroEncargadoReordenar::execute(['id_activ' => 1, 'id_ubi' => 1, 'num_orden' => 'arriba']));
    }

    public function test_mas_intercambia_con_anterior(): void
    {
        $a = new CentroEncargado();
        $a->setId_activ(1);
        $a->setId_ubi(10);
        $a->setNum_orden(1);
        $b = new CentroEncargado();
        $b->setId_activ(1);
        $b->setId_ubi(20);
        $b->setNum_orden(2);

        $repo = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $repo->method('getCentrosEncargados')->willReturn([$a, $b]);
        $repo->expects($this->exactly(2))->method('Guardar')->willReturn(true);

        $GLOBALS['container'] = new class($repo) {
            public function __construct(private readonly CentroEncargadoRepositoryInterface $repo) {}

            public function get(string $key): object
            {
                if ($key !== CentroEncargadoRepositoryInterface::class) {
                    throw new \RuntimeException('Clave inesperada: ' . $key);
                }

                return $this->repo;
            }
        };

        $this->assertSame('', CentroEncargadoReordenar::execute([
            'id_activ' => 1,
            'id_ubi' => 20,
            'num_orden' => 'mas',
        ]));

        $this->assertSame(2, (int) $a->getNum_orden());
        $this->assertSame(1, (int) $b->getNum_orden());
    }
}
