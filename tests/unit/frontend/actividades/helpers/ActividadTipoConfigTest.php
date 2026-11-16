<?php

declare(strict_types=1);

namespace Tests\unit\frontend\actividades\helpers;

use frontend\actividades\helpers\ActividadTipo;
use Tests\myTest;

/**
 * Configuración del widget de tipo de actividad desde los parámetros de formulario (las reglas del antiguo endpoint
 * `/src/actividades/actividad_que_datos`) y render desde esa configuración.
 */
final class ActividadTipoConfigTest extends myTest
{
    public function test_parametros_de_busqueda_extendida(): void
    {
        $c = ActividadTipo::configDesdeParametros([
            'perm_jefe' => 't', 'id_tipo_activ' => '112021', 'que' => 'buscar', 'sfsv' => 'sv', 'sasistentes' => 'n',
            'sactividad' => 'crt', 'sactividad2' => '10', 'snom_tipo' => 'x', 'extendida' => 't',
        ]);

        $this->assertSame([
            'perm_jefe' => true, 'id_tipo_activ' => 112021, 'que' => 'buscar', 'sfsv' => 'sv', 'asistentes' => 'n',
            'actividad' => '10', 'nom_tipo' => 'x', 'sfsv_all' => true, 'extendida' => true,
        ], $c);
    }

    public function test_sin_parametros_y_reglas_de_cada_campo(): void
    {
        $this->assertSame([
            'perm_jefe' => false, 'id_tipo_activ' => 0, 'sfsv' => '', 'asistentes' => '', 'actividad' => '', 'nom_tipo' => '',
            'sfsv_all' => true, 'extendida' => false,
        ], ActividadTipo::configDesdeParametros([]));

        $c = ActividadTipo::configDesdeParametros(['id_tipo_activ' => '11....', 'sactividad' => 'crt', 'sactividad2' => '10', 'sfsv_all' => 'f', 'para' => 'tipoactiv-tarifas', 'evitar_procesos' => 't']);
        $this->assertSame(['11....', 'crt', false, 'tipoactiv-tarifas'], [$c['id_tipo_activ'], $c['actividad'], $c['sfsv_all'], $c['para']]);
        $this->assertArrayNotHasKey('evitar_procesos', $c, 'el endpoint antiguo no lo leía');
    }

    public function test_el_render_depende_de_la_configuracion(): void
    {
        $normal = ActividadTipo::htmlDesdeConfig(['id_tipo_activ' => '131001', 'actividad' => 'crt']);
        $ext = ActividadTipo::htmlDesdeConfig(['id_tipo_activ' => '131001', 'actividad' => '01', 'extendida' => true]);

        $this->assertNotSame('', $normal);
        $this->assertNotSame($normal, $ext);
        $this->assertSame($normal, ActividadTipo::desdeConfig(['id_tipo_activ' => '131001', 'actividad' => 'crt'])->captureHtml());
    }
}
