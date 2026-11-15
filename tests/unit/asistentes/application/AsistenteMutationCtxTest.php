<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use src\asistentes\application\AsistenteMutationCtx;
use src\shared\security\HashB;
use Tests\myTest;

/** Cápsulas de las mutaciones de asistentes: qué fija cada una y cuándo no valen. */
final class AsistenteMutationCtxTest extends myTest
{
    public function test_guardar_fija_los_ids_conocidos_y_deja_libres_los_ceros(): void
    {
        $this->assertSame(['id_activ' => 3001, 'id_nom' => 0, 'id_activ_old' => 0], AsistenteMutationCtx::abrirGuardar(['ctx_guardar' => AsistenteMutationCtx::guardar(3001, 0)]));
        $this->assertSame(['id_activ' => 0, 'id_nom' => -1001123, 'id_activ_old' => 3001], AsistenteMutationCtx::abrirGuardar(['ctx_guardar' => AsistenteMutationCtx::guardar(0, -1001123, 3001)]), 'los ids de paso son negativos');
    }

    public function test_guardar_no_vale_sin_capsula_con_otra_accion_o_si_no_fija_nada(): void
    {
        $this->assertNull(AsistenteMutationCtx::abrirGuardar([]));
        $this->assertNull(AsistenteMutationCtx::abrirGuardar(['ctx_guardar' => 'x']));
        $this->assertNull(AsistenteMutationCtx::abrirGuardar(['ctx_guardar' => AsistenteMutationCtx::eliminar(3001, 10011)]), 'la cápsula de eliminar no sirve para guardar');
        $this->assertNull(AsistenteMutationCtx::abrirGuardar(['ctx_guardar' => AsistenteMutationCtx::guardar(0, 0, 3001)]), 'tiene que fijar la actividad o la persona');
    }

    public function test_eliminar_exige_actividad_y_persona(): void
    {
        $this->assertSame(['id_activ' => 3001, 'id_nom' => 10011], AsistenteMutationCtx::abrirEliminar(['ctx_eliminar_asistente' => AsistenteMutationCtx::eliminar(3001, 10011)]));
        $this->assertNull(AsistenteMutationCtx::abrirEliminar(['ctx_eliminar_asistente' => AsistenteMutationCtx::eliminar(3001, 0)]));
        $this->assertNull(AsistenteMutationCtx::abrirEliminar(['ctx_eliminar_asistente' => AsistenteMutationCtx::guardar(3001, 10011)]));
        $this->assertNull(AsistenteMutationCtx::abrirEliminar(['ctx_eliminar_cargo' => AsistenteMutationCtx::eliminar(3001, 10011)]), 'en otro campo no cuenta');
    }

    public function test_asignar_plaza_fija_la_actividad(): void
    {
        $this->assertSame(3001, AsistenteMutationCtx::abrirPlazaAsignar(['ctx_plaza' => AsistenteMutationCtx::plazaAsignar(3001)]));
        $this->assertNull(AsistenteMutationCtx::abrirPlazaAsignar(['ctx_plaza' => AsistenteMutationCtx::plazaAsignar(0)]));
        $this->assertNull(AsistenteMutationCtx::abrirPlazaAsignar(['ctx_plaza' => HashB::sign('asistente_guardar', ['id_activ' => 3001])]));
        $this->assertNull(AsistenteMutationCtx::abrirPlazaAsignar([]));
    }
}
