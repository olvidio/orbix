<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application;

use src\actividadcargos\application\CargoMutationCtx;
use Tests\myTest;

/** Cápsulas HashB de alta, edición y borrado de cargos: qué fijan y cuándo se rechazan. */
final class CargoMutationCtxTest extends myTest
{
    public function test_alta_fija_actividad_y_persona_y_no_deja_colar_un_id_item(): void
    {
        $post = ['ctx_guardar' => CargoMutationCtx::guardar(0, 3001, 0), 'id_activ' => '666', 'id_nom' => '100555', 'id_item' => '9', 'observ' => 'x'];
        $out = CargoMutationCtx::aplicarGuardar($post, CargoMutationCtx::NUEVO);
        $this->assertSame(3001, $out['id_activ'], 'la actividad sale de la cápsula');
        $this->assertSame('100555', $out['id_nom'], 'la persona la elige el usuario (0 en la cápsula)');
        $this->assertArrayNotHasKey('id_item', $out, 'un alta no edita otro cargo');
        $this->assertArrayNotHasKey('ctx_guardar', $out);
        $this->assertSame('x', $out['observ']);
    }

    public function test_edicion_fija_el_cargo_la_actividad_y_la_persona(): void
    {
        $post = ['ctx_guardar' => CargoMutationCtx::guardar(9, 3001, 100555), 'id_item' => '1', 'id_activ' => '2', 'id_nom' => '3'];
        $out = CargoMutationCtx::aplicarGuardar($post, CargoMutationCtx::EDITAR);
        $this->assertSame([9, 3001, 100555], [$out['id_item'], $out['id_activ'], $out['id_nom']]);
        $this->assertNull(CargoMutationCtx::aplicarGuardar($post, CargoMutationCtx::NUEVO), 'cápsula de edición usada para un alta');
        $this->assertNull(CargoMutationCtx::aplicarGuardar(['ctx_guardar' => 'x'], CargoMutationCtx::EDITAR));
        $this->assertNull(CargoMutationCtx::aplicarGuardar([], CargoMutationCtx::EDITAR));
    }

    public function test_borrado(): void
    {
        $this->assertSame(['id_item' => 9, 'elim_asis' => 1], CargoMutationCtx::abrirEliminar(['ctx_eliminar_cargo' => CargoMutationCtx::eliminar(9, 1)]));
        $this->assertNull(CargoMutationCtx::abrirEliminar(['ctx_eliminar_cargo' => CargoMutationCtx::eliminar(0, 1)]), 'sin cargo');
        $this->assertNull(CargoMutationCtx::abrirEliminar(['ctx_eliminar_cargo' => CargoMutationCtx::guardar(9, 1, 1)]), 'cápsula de otra acción');
        $this->assertNull(CargoMutationCtx::abrirEliminar([]));
    }
}
