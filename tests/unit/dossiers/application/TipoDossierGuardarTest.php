<?php

declare(strict_types=1);

namespace Tests\unit\dossiers\application;

use PHPUnit\Framework\TestCase;
use src\dossiers\application\TipoDossierGuardar;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\TipoDossier;

final class TipoDossierGuardarTest extends TestCase
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

    public function test_sin_id(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            TipoDossierRepositoryInterface::class => $this->createMock(TipoDossierRepositoryInterface::class),
        ]);

        $this->assertNotSame('', TipoDossierGuardar::execute([]));
    }

    public function test_no_encontrado(): void
    {
        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            TipoDossierRepositoryInterface::class => $repo,
        ]);

        $this->assertNotSame('', TipoDossierGuardar::execute(['id_tipo_dossier' => 2]));
    }

    public function test_falla_guardar(): void
    {
        $tipo = $this->tipoMinimo(3);

        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('findById')->willReturn($tipo);
        $repo->method('Guardar')->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            TipoDossierRepositoryInterface::class => $repo,
        ]);

        $this->assertNotSame('', TipoDossierGuardar::execute([
            'id_tipo_dossier' => 3,
            'tabla_from' => 'p',
        ]));
    }

    public function test_exito_aplica_campos_y_permisos(): void
    {
        $tipo = $this->tipoMinimo(7);

        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('findById')->with(7)->willReturn($tipo);
        $repo->expects($this->once())->method('Guardar')->with($tipo)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            TipoDossierRepositoryInterface::class => $repo,
        ]);

        $msg = TipoDossierGuardar::execute([
            'id_tipo_dossier' => 7,
            'descripcion' => 'Desc',
            'tabla_from' => 'p',
            'tabla_to' => 't1',
            'campo_to' => 'c1',
            'id_tipo_dossier_rel' => 4,
            'depende_modificar' => 't',
            'app' => 'mimod',
            'class' => 'MiClase',
            'codigo' => '  mi_slug  ',
            'Permiso_lectura' => [1, 4],
            'Permiso_escritura' => [2],
        ]);

        $this->assertSame('', $msg);
        $this->assertSame('Desc', $tipo->getDescripcion());
        $this->assertSame('p', $tipo->getTabla_from());
        $this->assertSame('t1', $tipo->getTabla_to());
        $this->assertSame('c1', $tipo->getCampo_to());
        $this->assertSame(4, $tipo->getId_tipo_dossier_rel());
        $this->assertTrue($tipo->isDepende_modificar());
        $this->assertSame('mimod', $tipo->getApp());
        $this->assertSame('MiClase', $tipo->getClass());
        $this->assertSame('mi_slug', $tipo->getCodigo());
        $this->assertSame(5, $tipo->getPermiso_lectura());
        $this->assertSame(2, $tipo->getPermiso_escritura());
    }

    private function tipoMinimo(int $id): TipoDossier
    {
        $o = new TipoDossier();
        $o->setId_tipo_dossier($id);
        $o->setTabla_from('x');
        $o->setPermiso_lectura(0);
        $o->setPermiso_escritura(0);
        $o->setDepende_modificar(false);
        return $o;
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
