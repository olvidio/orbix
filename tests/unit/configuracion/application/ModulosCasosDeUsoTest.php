<?php

declare(strict_types=1);

namespace Tests\unit\configuracion\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\configuracion\application\ModulosFormData;
use src\configuracion\application\ModulosSelectData;
use src\configuracion\application\ModulosUpdateAction;
use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\domain\entity\App;
use src\configuracion\domain\entity\Modulo;
use src\configuracion\domain\ModulosConfig;
use src\configuracion\domain\value_objects\AppsReq;
use src\configuracion\domain\value_objects\ModsReq;
use src\configuracion\domain\value_objects\ModuloName;
use src\shared\security\HashB;
use Tests\myTest;

/** Listado, formulario y guardado de módulos de la aplicación. */
final class ModulosCasosDeUsoTest extends myTest
{
    private ModuloRepositoryInterface&MockObject $repo;

    public function setUp(): void
    {
        parent::setUp();
        $this->repo = $this->createMock(ModuloRepositoryInterface::class);
    }

    private function modulo(int $id, string $nom, ?string $desc = null, ?array $mods = null, ?array $apps = null): Modulo
    {
        $m = new Modulo();
        $m->setIdModVo($id);
        $m->setNomVo(ModuloName::fromString($nom));
        $m->setDescripcionVo(\src\configuracion\domain\value_objects\ModuloDescription::fromNullableString($desc));
        $m->setModsReqVo(ModsReq::fromNullableArray($mods));
        $m->setAppsReqVo(AppsReq::fromNullableArray($apps));

        return $m;
    }

    // ---- ModulosSelectData -------------------------------------------------------------------

    private function select(array $modulos, array $apps = []): ModulosSelectData
    {
        $this->repo->method('matching')->willReturn($modulos);
        $appRepo = $this->createMock(AppRepositoryInterface::class);
        $appRepo->method('matching')->willReturn($apps);

        return new ModulosSelectData($this->repo, $appRepo);
    }

    private function app(int $id, string $nom): App
    {
        $a = new App();
        $a->setIdAppVo($id);
        $a->setNomVo($nom);

        return $a;
    }

    public function test_el_listado_traduce_ids_requeridos_a_nombres_y_firma_la_eliminacion(): void
    {
        $uc = $this->select(
            [$this->modulo(1, 'base'), $this->modulo(2, 'notas', 'las notas', [1, 9], [10, 11])],
            [$this->app(10, 'Personas')],
        );

        $r = $uc->execute([]);

        $this->assertSame(['1#', 'base', null, '', ''], [$r['a_valores'][1]['sel'], $r['a_valores'][1][1], $r['a_valores'][1][2], $r['a_valores'][1][3], $r['a_valores'][1][4]]);
        $this->assertSame(['2#', 'notas', 'las notas', 'base, 9', 'Personas, 11'], [
            $r['a_valores'][2]['sel'], $r['a_valores'][2][1], $r['a_valores'][2][2], $r['a_valores'][2][3], $r['a_valores'][2][4],
        ]);
        $this->assertSame(['id_mod' => 2], HashB::open($r['a_valores'][2]['ctx_eliminar'], 'modulos_eliminar'));
        $this->assertCount(4, $r['a_cabeceras']);
        $this->assertCount(2, $r['a_botones']);
        $this->assertArrayNotHasKey('select', $r['a_valores']);
    }

    public function test_el_listado_recupera_seleccion_y_scroll_de_la_pila_solo_si_hay_pila(): void
    {
        $uc = $this->select([]);

        $con = $uc->execute(['stack' => '2', 'restored_id_sel' => '7#', 'restored_scroll_id' => '33', 'id_sel' => 'x']);
        $this->assertSame(['7#', '33'], [$con['a_valores']['select'], $con['a_valores']['scroll_id']]);

        $sin = $uc->execute(['stack' => '0', 'restored_id_sel' => '7#', 'id_sel' => 'x', 'scroll_id' => 's']);
        $this->assertSame(['x', 's'], [$sin['a_valores']['select'], $sin['a_valores']['scroll_id']]);
    }

    // ---- ModulosFormData ---------------------------------------------------------------------

    private function form(): ModulosFormData
    {
        $cfg = $this->createMock(ModulosConfig::class);
        $cfg->method('getModsAll')->willReturn([1 => 'base', 2 => 'notas']);
        $cfg->method('getAppsAll')->willReturn([10 => 'Personas']);
        $cfg->method('getAppsMods')->willReturnCallback(static fn (int $m): array => $m === 1 ? [10, 11] : [11, 12]);

        return new ModulosFormData($this->repo, $cfg);
    }

    public function test_un_modulo_nuevo_trae_el_formulario_vacio_sin_consultar_el_repositorio(): void
    {
        $this->repo->expects($this->never())->method('findById');

        $r = $this->form()->execute(['mod' => 'nuevo']);

        $this->assertSame([0, '', '', [], []], [$r['id_mod'], $r['nom'], $r['descripcion'], $r['a_mods_req'], $r['a_apps_mod']]);
        $this->assertSame(['id_mod' => 0, 'mod' => 'nuevo'], HashB::open($r['hash_main']['campos_hidden']['ctx_guardar'], 'modulos_guardar'));
        $this->assertSame([1 => 'base', 2 => 'notas'], $r['a_mods_todos']);
    }

    public function test_modificar_carga_el_modulo_y_las_apps_de_sus_modulos_requeridos_sin_repetir(): void
    {
        $this->repo->method('findById')->with(2)->willReturn($this->modulo(2, 'notas', 'las notas', [1, 2], [10]));

        $r = $this->form()->execute(['sel' => ['2#'], 'mod' => '']);

        $this->assertEquals([2, 'notas', 'las notas', [1, 2], [10]], [$r['id_mod'], $r['nom'], $r['descripcion'], $r['a_mods_req'], $r['a_apps_req']]);
        $this->assertSame([10, 11, 12], $r['a_apps_mod']);
        $this->assertSame(['id_mod' => 2, 'mod' => ''], HashB::open($r['hash_main']['campos_hidden']['ctx_guardar'], 'modulos_guardar'));
    }

    public function test_sin_seleccion_se_usa_id_mod_y_un_modulo_inexistente_deja_los_campos_vacios(): void
    {
        $this->repo->method('findById')->with(5)->willReturn(null);

        $r = $this->form()->execute(['id_mod' => '5']);

        $this->assertSame([5, ''], [$r['id_mod'], $r['nom']]);
    }

    // ---- ModulosUpdateAction -----------------------------------------------------------------

    public function test_alta_crea_el_modulo_con_id_nuevo_y_listas_normalizadas(): void
    {
        $this->repo->method('getNewId')->willReturn(40);
        $guardado = null;
        $this->repo->expects($this->once())->method('Guardar')->willReturnCallback(function (Modulo $m) use (&$guardado): bool {
            $guardado = $m;

            return true;
        });

        $r = (new ModulosUpdateAction($this->repo))->execute([
            'mod' => 'nuevo', 'nom' => 'nuevo mod', 'descripcion' => 'd', 'sel_mods' => ['1', 'x', 2], 'sel_apps' => [],
        ]);

        $this->assertSame('', $r);
        $this->assertEquals([40, 'nuevo mod', 'd', [1, 2], null], [
            $guardado->getId_mod(), $guardado->getNom(), $guardado->getDescripcion(), $guardado->getMods_req(), $guardado->getApps_req(),
        ]);
    }

    public function test_alta_sin_nombre_no_guarda(): void
    {
        $this->repo->expects($this->never())->method('Guardar');

        $this->assertSame('', (new ModulosUpdateAction($this->repo))->execute(['mod' => 'nuevo', 'nom' => '']));
    }

    public function test_eliminar_inexistente_no_hace_nada_y_un_fallo_devuelve_el_texto_de_error(): void
    {
        $this->repo->method('findById')->willReturnOnConsecutiveCalls(null, $this->modulo(3, 'x'));
        $this->repo->method('Eliminar')->willReturn(false);
        $this->repo->method('getErrorTxt')->willReturn('fk violada');
        $uc = new ModulosUpdateAction($this->repo);

        $this->assertSame('', $uc->execute(['mod' => 'eliminar', 'id_mod' => '3']));
        $this->assertStringContainsString('fk violada', $uc->execute(['mod' => 'eliminar', 'id_mod' => '3']));
    }

    public function test_eliminar_correcto_responde_vacio_y_la_seleccion_manda_sobre_id_mod(): void
    {
        $this->repo->expects($this->once())->method('findById')->with(8)->willReturn($this->modulo(8, 'x'));
        $this->repo->expects($this->once())->method('Eliminar')->willReturn(true);

        $this->assertSame('', (new ModulosUpdateAction($this->repo))->execute(['mod' => 'eliminar', 'id_mod' => '3', 'sel' => ['8#']]));
    }

    public function test_modificar_actualiza_los_campos_del_modulo_existente(): void
    {
        $m = $this->modulo(2, 'viejo', 'x', [1], [10]);
        $this->repo->method('findById')->with(2)->willReturn($m);
        $this->repo->expects($this->once())->method('Guardar')->with($m);

        (new ModulosUpdateAction($this->repo))->execute(['id_mod' => '2', 'nom' => 'nuevo', 'descripcion' => '', 'sel_apps' => ['11']]);

        $this->assertEquals(['nuevo', null, [11]], [$m->getNom(), $m->getDescripcion() ?: null, $m->getApps_req()]);
        $this->assertNull($m->getMods_req(), 'sin sel_mods se vacían los módulos requeridos');
    }

    public function test_modificar_un_modulo_inexistente_no_guarda(): void
    {
        $this->repo->method('findById')->willReturn(null);
        $this->repo->expects($this->never())->method('Guardar');

        $this->assertSame('', (new ModulosUpdateAction($this->repo))->execute(['id_mod' => '2', 'nom' => 'n']));
    }
}
