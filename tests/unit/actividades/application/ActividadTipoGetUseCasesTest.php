<?php

namespace Tests\unit\actividades\application;

use src\actividades\application\ActividadTipoGetActividad;
use src\actividades\application\ActividadTipoGetAsistentes;
use src\actividades\application\ActividadTipoGetFiltroLugar;
use src\actividades\application\ActividadTipoGetNomTipo;
use src\actividades\application\ActividadTipoGetNivelStgrDefecto;
use src\actividades\domain\value_objects\NivelStgrId;
use Tests\myTest;

final class ActividadTipoGetUseCasesTest extends myTest
{
    public function test_actividad_tipo_get_asistentes_estructura_y_accion(): void
    {
        $out = (new ActividadTipoGetAsistentes())->execute([
            'entrada' => '1',
            'extendida' => '',
        ]);

        $this->assertSame('iasistentes_val', $out['id']);
        $this->assertArrayHasKey('opciones', $out);
        $this->assertIsArray($out['opciones']);
        $this->assertSame('.', $out['selected']);
        $this->assertSame('.', $out['val_blanco']);
        $this->assertStringContainsString('fnjs_actividad(false)', $out['action']);
    }

    public function test_actividad_tipo_get_asistentes_extendida_true_en_action(): void
    {
        $out = (new ActividadTipoGetAsistentes())->execute([
            'entrada' => '1',
            'extendida' => 't',
        ]);
        $this->assertStringContainsString('fnjs_actividad(true)', $out['action']);
    }

    public function test_actividad_tipo_get_actividad_extendida_valores_blanco(): void
    {
        $normal = (new ActividadTipoGetActividad())->execute(['entrada' => '1', 'extendida' => '']);
        $this->assertSame('iactividad_val', $normal['id']);
        $this->assertTrue($normal['blanco']);
        $this->assertSame('.', $normal['val_blanco']);
        $this->assertSame('.', $normal['selected']);

        $ext = (new ActividadTipoGetActividad())->execute(['entrada' => '1', 'extendida' => 't']);
        $this->assertSame('..', $ext['val_blanco']);
        $this->assertSame('..', $ext['selected']);
        $this->assertSame('fnjs_nom_tipo()', $ext['action']);
    }

    public function test_actividad_tipo_get_nom_tipo_modo_buscar_vs_otro(): void
    {
        $buscar = (new ActividadTipoGetNomTipo())->execute([
            'entrada' => '1',
            'extendida' => '',
            'modo' => 'buscar',
        ]);
        $this->assertSame('fnjs_id_activ()', $buscar['action']);

        $otro = (new ActividadTipoGetNomTipo())->execute([
            'entrada' => '1',
            'extendida' => '',
            'modo' => 'gestion',
        ]);
        $this->assertSame('fnjs_act_id_activ()', $otro['action']);
    }

    public function test_actividad_tipo_get_filtro_lugar(): void
    {
        $out = $GLOBALS['container']->get(ActividadTipoGetFiltroLugar::class)->execute(['entrada' => '1']);
        $this->assertSame('filtro_lugar', $out['id']);
        $this->assertTrue($out['blanco']);
        $this->assertSame('fnjs_lugar()', $out['action']);
        $this->assertArrayHasKey('opciones', $out);
    }

    public function test_actividad_tipo_get_nivel_stgr_defecto_vacio_es_sin_estudios(): void
    {
        $out = (new ActividadTipoGetNivelStgrDefecto())->execute(['entrada' => '']);
        $this->assertSame((string)NivelStgrId::N, $out);
    }
}
