<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\shared\security\HashB;

final class CuadriculaCtxUpdateTest extends TestCase
{
    protected function setUp(): void
    {
        session_id('test-session-misas-cuadricula');
        if (!function_exists('misas_cuadricula_ctx_update')) {
            require_once dirname(__DIR__, 4) . '/src/misas/application/cuadricula_zona_grid_data_build.php';
        }
    }

    public function test_helper_firma_zona_y_plantilla(): void
    {
        $ctx = \misas_cuadricula_ctx_update(9, 's1');
        $this->assertSame(
            ['id_zona' => 9, 'tipo_plantilla' => 's1'],
            HashB::open($ctx, 'cuadricula_update')
        );
    }
}
