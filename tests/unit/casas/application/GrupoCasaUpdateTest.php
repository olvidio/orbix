<?php

namespace Tests\unit\casas\application;

use PHPUnit\Framework\TestCase;
use src\casas\application\GrupoCasaUpdate;
use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\casas\domain\entity\GrupoCasa;

final class GrupoCasaUpdateTest extends TestCase
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

    public function test_faltan_casas(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            GrupoCasaRepositoryInterface::class => $this->createMock(GrupoCasaRepositoryInterface::class),
        ]);

        $this->assertNotSame('', GrupoCasaUpdate::execute([
            'id_item' => 'nuevo',
            'id_ubi_padre' => 0,
            'id_ubi_hijo' => 3,
        ]));
    }

    public function test_misma_casa(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            GrupoCasaRepositoryInterface::class => $this->createMock(GrupoCasaRepositoryInterface::class),
        ]);

        $this->assertNotSame('', GrupoCasaUpdate::execute([
            'id_item' => 'nuevo',
            'id_ubi_padre' => 4,
            'id_ubi_hijo' => 4,
        ]));
    }

    public function test_nuevo_getNewId_y_guardar(): void
    {
        $oGrupo = new GrupoCasa();

        $repo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repo->method('getNewId')->willReturn(100);
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(function (GrupoCasa $g) {
            $this->assertSame(100, $g->getId_item());
            $this->assertSame(1, $g->getId_ubi_padre());
            $this->assertSame(2, $g->getId_ubi_hijo());
            return true;
        });

        $GLOBALS['container'] = $this->containerFromMap([
            GrupoCasaRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', GrupoCasaUpdate::execute([
            'id_item' => 'nuevo',
            'id_ubi_padre' => 1,
            'id_ubi_hijo' => 2,
        ]));
    }

    public function test_edita_grupo_no_encontrado(): void
    {
        $repo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repo->method('findById')->with(7)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            GrupoCasaRepositoryInterface::class => $repo,
        ]);

        $this->assertNotSame('', GrupoCasaUpdate::execute([
            'id_item' => '7',
            'id_ubi_padre' => 1,
            'id_ubi_hijo' => 2,
        ]));
    }

    public function test_falla_guardar(): void
    {
        $existente = new GrupoCasa();
        $existente->setId_item(7);

        $repo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repo->method('findById')->willReturn($existente);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db');

        $GLOBALS['container'] = $this->containerFromMap([
            GrupoCasaRepositoryInterface::class => $repo,
        ]);

        $msg = GrupoCasaUpdate::execute([
            'id_item' => '7',
            'id_ubi_padre' => 3,
            'id_ubi_hijo' => 4,
        ]);
        $this->assertNotSame('', $msg);
        $this->assertStringContainsString('db', $msg);
    }

    public function test_exito_edicion(): void
    {
        $existente = new GrupoCasa();
        $existente->setId_item(7);

        $repo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repo->method('findById')->willReturn($existente);
        $repo->method('Guardar')->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            GrupoCasaRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', GrupoCasaUpdate::execute([
            'id_item' => '7',
            'id_ubi_padre' => 3,
            'id_ubi_hijo' => 4,
        ]));
        $this->assertSame(3, $existente->getId_ubi_padre());
        $this->assertSame(4, $existente->getId_ubi_hijo());
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
