<?php

declare(strict_types=1);

namespace Tests\unit\frontend\web;

use frontend\shared\web\Lista;
use PHPUnit\Framework\TestCase;

class ListaSlickgridMetaTest extends TestCase
{
    public function test_ctx_sale_de_la_fila_y_queda_en_meta(): void
    {
        $out = Lista::extraerMetaSlickgrid([
            'sel' => 'abc',
            1 => 'agdAubens',
            2 => 'B 100',
            'ctx_eliminar' => 'token-eliminar',
            'ctx_trasladar' => 'token-trasladar',
        ]);

        $this->assertSame('abc', $out['fila']['sel']);
        $this->assertSame('agdAubens', $out['fila'][1]);
        $this->assertSame('B 100', $out['fila'][2]);
        $this->assertArrayNotHasKey('ctx_eliminar', $out['fila']);
        $this->assertArrayNotHasKey('ctx_trasladar', $out['fila']);
        $this->assertSame([
            'ctx_eliminar' => 'token-eliminar',
            'ctx_trasladar' => 'token-trasladar',
        ], $out['meta']);
    }

    public function test_clave_que_no_es_capsula_sigue_en_la_fila(): void
    {
        $out = Lista::extraerMetaSlickgrid([
            'sel' => 'id#',
            'clase' => 'imp',
            1 => 'dato',
        ]);

        $this->assertSame([], $out['meta']);
        $this->assertSame('imp', $out['fila']['clase']);
        $this->assertSame('dato', $out['fila'][1]);
    }
}
