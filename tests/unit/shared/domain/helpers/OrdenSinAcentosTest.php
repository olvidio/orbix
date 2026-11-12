<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\helpers;

use PHPUnit\Framework\TestCase;
use src\shared\domain\helpers\FuncTablasSupport;

/** Ordenar nombres sin tener en cuenta acentos ni mayúsculas (con la ñ detrás de la n). */
final class OrdenSinAcentosTest extends TestCase
{
    public function test_claves_para_array_multisort_con_el_mismo_orden_que_strsinacentocmp(): void
    {
        $nombres = ['Zapata', 'Álvarez', 'bueno', 'Núñez', 'Nuno', 'Ortiz', 'Óscar'];
        $filas = $nombres;
        $claves = FuncTablasSupport::clavesOrdenSinAcentos($nombres);
        array_multisort($claves, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $filas);

        $esperado = $nombres;
        usort($esperado, [FuncTablasSupport::class, 'strsinacentocmp']);
        $this->assertSame($esperado, $filas);
        $this->assertSame(['Álvarez', 'bueno', 'Nuno', 'Núñez', 'Ortiz', 'Óscar', 'Zapata'], $filas);
    }

    public function test_sin_acentos(): void
    {
        $this->assertSame('alvarez Nunzez', FuncTablasSupport::sinAcentosParaOrdenar('Álvarez Núñez'));
        $this->assertSame(['a' => 'eso', 'b' => ''], FuncTablasSupport::clavesOrdenSinAcentos(['a' => 'éso', 'b' => null]));
    }
}
