<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadMutationCtx;
use src\shared\security\HashB;

final class ActividadMutationCtxTest extends TestCase
{
    public function test_row_tokens_default_eliminar_y_duplicar(): void
    {
        $tokens = ActividadMutationCtx::rowTokens(42, '');
        $this->assertSame(['id_activ' => 42], HashB::open($tokens['ctx_eliminar'], 'actividad_eliminar'));
        $this->assertSame(['id_activ' => 42], HashB::open($tokens['ctx_duplicar'], 'actividad_duplicar'));
        $this->assertArrayNotHasKey('ctx_publicar', $tokens);
        $this->assertArrayNotHasKey('ctx_importar', $tokens);
    }

    public function test_row_tokens_publicar_e_importar(): void
    {
        $pub = ActividadMutationCtx::rowTokens(7, 'publicar');
        $this->assertSame(['id_activ' => 7], HashB::open($pub['ctx_publicar'], 'actividad_publicar'));
        $this->assertCount(1, $pub);

        $imp = ActividadMutationCtx::rowTokens(9, 'importar');
        $this->assertSame(['id_activ' => 9], HashB::open($imp['ctx_importar'], 'actividad_importar'));
        $this->assertCount(1, $imp);
    }

    public function test_open_sel_ids_descarta_capsulas_invalidas(): void
    {
        $ok = HashB::sign('actividad_eliminar', ['id_activ' => 15]);
        $ids = ActividadMutationCtx::openSelIds([$ok, 'no-es-capsula'], 'actividad_eliminar');
        $this->assertSame(['15'], $ids);
    }

    public function test_id_activ_cero_sin_tokens(): void
    {
        $this->assertSame([], ActividadMutationCtx::rowTokens(0, ''));
    }
}
