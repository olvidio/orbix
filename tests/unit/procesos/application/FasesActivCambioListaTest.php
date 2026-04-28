<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\procesos\application\FasesActivCambioLista;

/**
 * Test unitario del camino de retorno temprano del caso de uso
 * FasesActivCambioLista (id_fase_nueva vacío).
 */
final class FasesActivCambioListaTest extends TestCase
{
    public function test_sin_id_fase_nueva_devuelve_mensaje_error(): void
    {
        $data = (new FasesActivCambioLista())->execute([]);
        $this->assertIsArray($data);
        $this->assertSame(_('Debe poner la fase nueva'), $data['error']);
    }

    public function test_id_fase_nueva_vacia_devuelve_mensaje_error(): void
    {
        $data = (new FasesActivCambioLista())->execute([
            'id_fase_nueva' => '',
            'accion' => 'marcar',
        ]);
        $this->assertIsArray($data);
        $this->assertSame(_('Debe poner la fase nueva'), $data['error']);
        $this->assertSame('marcar', $data['accion']);
    }
}
