<?php

declare(strict_types=1);

namespace Tests\unit\actividadescentro\application;

use src\actividadescentro\application\CentroEncargadoMutationCtx as Ctx;
use Tests\myTest;

/** Cápsulas HashB para asignar, reordenar y quitar centros encargados de una actividad. */
final class CentroEncargadoMutationCtxTest extends myTest
{
    public function test_cada_capsula_abre_solo_su_accion_con_actividad_y_centro(): void
    {
        $de = Ctx::deEncargado(3001, 1010810);
        $this->assertSame(['id_activ' => 3001, 'id_ubi' => 1010810], Ctx::abrir(['ctx' => $de['ctx_reordenar']], Ctx::REORDENAR));
        $this->assertSame(['id_activ' => 3001, 'id_ubi' => 1010810], Ctx::abrir(['ctx' => $de['ctx_eliminar']], Ctx::ELIMINAR));
        $this->assertNull(Ctx::abrir(['ctx' => $de['ctx_reordenar']], Ctx::ELIMINAR), 'la de reordenar no sirve para quitar');
        $this->assertSame(['id_activ' => 3001, 'id_ubi' => 5], Ctx::abrir(['ctx' => Ctx::firmar(Ctx::ASIGNAR, 3001, 5)], Ctx::ASIGNAR));
    }

    public function test_capsulas_no_validas(): void
    {
        $this->assertNull(Ctx::abrir(['ctx' => Ctx::firmar(Ctx::ASIGNAR, 0, 5)], Ctx::ASIGNAR), 'sin actividad');
        $this->assertNull(Ctx::abrir(['ctx' => Ctx::firmar(Ctx::ASIGNAR, 3001, 0)], Ctx::ASIGNAR), 'sin centro');
        $this->assertNull(Ctx::abrir(['ctx' => 'manipulada'], Ctx::ASIGNAR));
        $this->assertNull(Ctx::abrir([], Ctx::ASIGNAR));
    }
}
