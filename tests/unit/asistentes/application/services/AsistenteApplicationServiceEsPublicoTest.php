<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application\services;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\shared\domain\contracts\UnitOfWorkInterface;

/**
 * Caso A: guardar asistencia ya no marca publicación; los listados resuelven por global.personas.
 */
final class AsistenteApplicationServiceEsPublicoTest extends TestCase
{
    public function test_guardar_no_marca_publicacion_cross_dl(): void
    {
        $asistente = new Asistente();
        $asistente->setId_activ(100);
        $asistente->setId_nom(200);

        $repo = $this->createMock(AsistenteRepositoryInterface::class);
        $repo->method('findById')->with(100, 200)->willReturn($asistente);
        $repo->expects($this->once())->method('Guardar')->with($asistente)->willReturn(true);

        $uow = $this->createMock(UnitOfWorkInterface::class);
        $uow->expects($this->once())->method('execute')->willReturnCallback(
            static function (callable $callback) {
                return $callback(new class {
                    public function registerEntity(object $entity): void
                    {
                    }
                });
            }
        );

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnCallback(function (string $id) use ($repo) {
            if ($id === AsistenteDlRepositoryInterface::class) {
                return $repo;
            }

            return $this->createMock($id);
        });

        $service = new AsistenteApplicationService($repo, $uow, $container);

        $this->assertTrue($service->guardar($asistente));
    }

    public function test_findById_si_la_vista_no_lo_ve_lo_busca_en_out(): void
    {
        $asistente = new Asistente();
        $asistente->setId_activ(100411479);
        $asistente->setId_nom(100516905);

        $vista = $this->createMock(AsistenteRepositoryInterface::class);
        $vista->method('findById')->with(100411479, 100516905)->willReturn(null);

        $out = $this->createMock(AsistenteOutRepositoryInterface::class);
        $out->method('findById')->with(100411479, 100516905)->willReturn($asistente);

        $vacio = $this->createMock(AsistenteRepositoryInterface::class);
        $vacio->method('findById')->willReturn(null);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnCallback(function (string $id) use ($out, $vacio) {
            return $id === AsistenteOutRepositoryInterface::class ? $out : $vacio;
        });

        $service = new AsistenteApplicationService(
            $vista,
            $this->createMock(UnitOfWorkInterface::class),
            $container,
        );

        $this->assertSame($asistente, $service->findById(100411479, 100516905));
    }

    private function uowQueEjecuta(): UnitOfWorkInterface
    {
        $uow = $this->createMock(UnitOfWorkInterface::class);
        $uow->method('execute')->willReturnCallback(static fn (callable $cb) => $cb(new class {
            /** @var list<object> */
            public array $registradas = [];

            public function registerEntity(object $entity): void
            {
                $this->registradas[] = $entity;
            }
        }));

        return $uow;
    }

    /**
     * @param array<class-string, AsistenteRepositoryInterface> $repos repos hijos por interfaz (el resto, vacíos)
     */
    private function contenedor(array $repos, ?\src\asistentes\application\services\AsistenteActividadService $actividad = null): ContainerInterface
    {
        $vacio = $this->createMock(AsistenteRepositoryInterface::class);
        $vacio->method('findById')->willReturn(null);
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnCallback(static function (string $id) use ($repos, $vacio, $actividad): object {
            if ($id === \src\asistentes\application\services\AsistenteActividadService::class && $actividad !== null) {
                return $actividad;
            }

            return $repos[$id] ?? $vacio;
        });

        return $container;
    }

    private function asistente(int $idActiv = 100, int $idNom = 200): Asistente
    {
        $a = new Asistente();
        $a->setId_activ($idActiv);
        $a->setId_nom($idNom);

        return $a;
    }

    public function test_find_by_id_devuelve_lo_que_encuentra_la_vista_sin_barrer_las_tablas(): void
    {
        $a = $this->asistente();
        $vista = $this->createMock(AsistenteRepositoryInterface::class);
        $vista->method('findById')->willReturn($a);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->never())->method('get');

        $this->assertSame($a, (new AsistenteApplicationService($vista, $this->uowQueEjecuta(), $container))->findById(100, 200));
    }

    public function test_find_by_id_sin_rastro_en_ninguna_tabla_devuelve_null(): void
    {
        $vista = $this->createMock(AsistenteRepositoryInterface::class);
        $vista->method('findById')->willReturn(null);

        $this->assertNull((new AsistenteApplicationService($vista, $this->uowQueEjecuta(), $this->contenedor([])))->findById(100, 200));
    }

    public function test_un_asistente_nuevo_se_guarda_en_la_tabla_que_decide_el_servicio_de_actividades(): void
    {
        $a = $this->asistente();
        $out = $this->createMock(AsistenteOutRepositoryInterface::class);
        $out->method('findById')->willReturn(null);
        $out->expects($this->once())->method('Guardar')->with($a)->willReturn(true);
        $actividad = $this->createMock(\src\asistentes\application\services\AsistenteActividadService::class);
        $actividad->expects($this->once())->method('getRepoAsistente')->with(200, 100)->willReturn(AsistenteOutRepositoryInterface::class);
        $vista = $this->createMock(AsistenteRepositoryInterface::class);
        $vista->expects($this->never())->method('Guardar');

        $service = new AsistenteApplicationService($vista, $this->uowQueEjecuta(), $this->contenedor([AsistenteOutRepositoryInterface::class => $out], $actividad));

        $this->assertTrue($service->guardar($a));
    }

    public function test_un_asistente_existente_se_actualiza_en_la_tabla_donde_esta(): void
    {
        $a = $this->asistente();
        $ex = $this->createMock(\src\asistentes\domain\contracts\AsistenteExRepositoryInterface::class);
        $ex->method('findById')->willReturn($a);
        $ex->expects($this->once())->method('Guardar')->with($a)->willReturn(false);
        $actividad = $this->createMock(\src\asistentes\application\services\AsistenteActividadService::class);
        $actividad->expects($this->never())->method('getRepoAsistente');

        $service = new AsistenteApplicationService($this->createMock(AsistenteRepositoryInterface::class), $this->uowQueEjecuta(), $this->contenedor([\src\asistentes\domain\contracts\AsistenteExRepositoryInterface::class => $ex], $actividad));

        $this->assertFalse($service->guardar($a));
    }

    public function test_eliminar_borra_en_la_tabla_donde_esta_y_si_no_esta_en_ninguna_devuelve_false(): void
    {
        $a = $this->asistente();
        $out = $this->createMock(AsistenteOutRepositoryInterface::class);
        $out->method('findById')->willReturn($a);
        $out->expects($this->once())->method('Eliminar')->with($a)->willReturn(true);
        $vista = $this->createMock(AsistenteRepositoryInterface::class);
        $vista->expects($this->never())->method('Eliminar');

        $this->assertTrue((new AsistenteApplicationService($vista, $this->uowQueEjecuta(), $this->contenedor([AsistenteOutRepositoryInterface::class => $out])))->eliminar($a));
        $this->assertFalse((new AsistenteApplicationService($vista, $this->uowQueEjecuta(), $this->contenedor([])))->eliminar($a));
    }

    public function test_eliminar_por_id(): void
    {
        $a = $this->asistente();
        $vista = $this->createMock(AsistenteRepositoryInterface::class);
        $vista->method('findById')->willReturnCallback(static fn (int $i): ?Asistente => $i === 100 ? $a : null);
        $dl = $this->createMock(AsistenteDlRepositoryInterface::class);
        $dl->method('findById')->willReturnCallback(static fn (int $i): ?Asistente => $i === 100 ? $a : null);
        $dl->method('Eliminar')->willReturn(true);
        $service = new AsistenteApplicationService($vista, $this->uowQueEjecuta(), $this->contenedor([AsistenteDlRepositoryInterface::class => $dl]));

        $this->assertTrue($service->eliminarById(100, 200));
        $this->assertFalse($service->eliminarById(999, 200));
    }

    /**
     * Regresión: `guardar()` y `eliminar()` dejaban el repositorio de la tabla hija en `$this->repository`, el
     * mismo que usan `matching()` y `findById()` para leer de la vista general. Como el contenedor crea el servicio
     * una sola vez, tras el primer guardado de una petición `matching()` consultaba solo esa tabla.
     */
    public function test_guardar_no_cambia_el_repositorio_con_el_que_se_lee_despues(): void
    {
        $a = $this->asistente();
        $vista = $this->createMock(AsistenteRepositoryInterface::class);
        $vista->expects($this->once())->method('matching')->willReturn([$a]);
        $out = $this->createMock(AsistenteOutRepositoryInterface::class);
        $out->method('findById')->willReturn($a);
        $out->method('Guardar')->willReturn(true);
        $out->method('Eliminar')->willReturn(true);
        $out->expects($this->never())->method('matching');
        $service = new AsistenteApplicationService($vista, $this->uowQueEjecuta(), $this->contenedor([AsistenteOutRepositoryInterface::class => $out]));

        $service->guardar($a);
        $service->eliminar($a);

        $this->assertSame([$a], $service->matching(\src\shared\domain\criteria\Criteria::create()));
    }
}
