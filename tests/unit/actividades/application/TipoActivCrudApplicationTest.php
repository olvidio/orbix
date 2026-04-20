<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\TipoActivEliminar;
use src\actividades\application\TipoActivLista;
use src\actividades\application\TipoActivNuevo;
use src\actividades\application\TipoActivUpdate;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TipoDeActividad;
use src\actividades\domain\value_objects\TipoActivNombre;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;

final class TipoActivCrudApplicationTest extends TestCase
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

    public function test_tipo_activ_nuevo_id_incorrecto_anexa_mensaje(): void
    {
        $repo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $repo->expects($this->once())->method('Guardar')->willReturn(true);

        $GLOBALS['container'] = $this->containerOne(TipoDeActividadRepositoryInterface::class, $repo);

        $out = (new TipoActivNuevo())->execute([
            'isfsv_val' => '1',
            'iasistentes_val' => '1',
            'iactividad_val' => '1',
            'id_nom_tipo_activ' => '1',
            'nom_tipo_activ' => 'x',
        ]);
        $this->assertStringContainsString('Id incorrecto', $out);
    }

    public function test_tipo_activ_nuevo_guardar_falso_anexa_error(): void
    {
        $repo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $repo->method('Guardar')->willReturn(false);

        $GLOBALS['container'] = $this->containerOne(TipoDeActividadRepositoryInterface::class, $repo);

        $out = (new TipoActivNuevo())->execute([
            'isfsv_val' => '1',
            'iasistentes_val' => '1',
            'iactividad_val' => '1',
            'id_nom_tipo_activ' => '23',
            'nom_tipo_activ' => 'Nombre',
        ]);
        $this->assertStringContainsString('no se ha guardado', $out);
    }

    public function test_tipo_activ_update_error_si_guardar_falla(): void
    {
        $entity = new TipoDeActividad();
        $entity->setId_tipo_activ(123456);
        $entity->setNombreVo(new TipoActivNombre('old'));

        $repo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $repo->method('findById')->with(123456)->willReturn($entity);
        $repo->method('Guardar')->willReturn(false);

        $GLOBALS['container'] = $this->containerOne(TipoDeActividadRepositoryInterface::class, $repo);

        $msg = (new TipoActivUpdate())->execute([
            'id_tipo_activ' => 123456,
            'nom_tipo_activ' => 'new',
        ]);
        $this->assertStringContainsString('no se ha guardado', $msg);
    }

    public function test_tipo_activ_update_ok_devuelve_vacio(): void
    {
        $entity = new TipoDeActividad();
        $entity->setId_tipo_activ(123456);
        $entity->setNombreVo(new TipoActivNombre('old'));

        $repo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $repo->method('findById')->willReturn($entity);
        $repo->method('Guardar')->willReturn(true);

        $GLOBALS['container'] = $this->containerOne(TipoDeActividadRepositoryInterface::class, $repo);

        $this->assertSame('', (new TipoActivUpdate())->execute([
            'id_tipo_activ' => 123456,
            'nom_tipo_activ' => 'new',
        ]));
    }

    public function test_tipo_activ_eliminar_error_si_falla(): void
    {
        $entity = new TipoDeActividad();
        $entity->setId_tipo_activ(123456);

        $repo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $repo->method('findById')->willReturn($entity);
        $repo->method('Eliminar')->willReturn(false);

        $GLOBALS['container'] = $this->containerOne(TipoDeActividadRepositoryInterface::class, $repo);

        $msg = (new TipoActivEliminar())->execute(['id_tipo_activ' => 123456]);
        $this->assertStringContainsString('no se ha eliminado', $msg);
    }

    public function test_tipo_activ_eliminar_ok(): void
    {
        $entity = new TipoDeActividad();
        $entity->setId_tipo_activ(123456);

        $repo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $repo->method('findById')->willReturn($entity);
        $repo->method('Eliminar')->willReturn(true);

        $GLOBALS['container'] = $this->containerOne(TipoDeActividadRepositoryInterface::class, $repo);

        $this->assertSame('', (new TipoActivEliminar())->execute(['id_tipo_activ' => 123456]));
    }

    public function test_tipo_activ_lista_html_con_repositorio_vacio(): void
    {
        $repo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $repo->method('getTiposDeActividades')->willReturn([]);

        $prefRepo = $this->createMock(PreferenciaRepositoryInterface::class);

        $GLOBALS['container'] = new class($repo, $prefRepo) {
            public function __construct(
                private readonly object $tipoRepo,
                private readonly object $prefRepo
            ) {}

            public function get(string $id): object
            {
                return match ($id) {
                    TipoDeActividadRepositoryInterface::class => $this->tipoRepo,
                    PreferenciaRepositoryInterface::class => $this->prefRepo,
                    default => throw new \RuntimeException('Unexpected DI key: ' . $id),
                };
            }
        };

        $html = (new TipoActivLista())->execute();
        $this->assertNotSame('', $html);
    }

    /**
     * @param class-string $iface
     */
    private function containerOne(string $iface, object $service): object
    {
        return new class($iface, $service) {
            public function __construct(
                private readonly string $iface,
                private readonly object $service
            ) {}

            public function get(string $id): object
            {
                if ($id !== $this->iface) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->service;
            }
        };
    }
}
