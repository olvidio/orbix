<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\helpers;

use PHPUnit\Framework\TestCase;

final class UsortProfesoresPorApellidosTest extends TestCase
{
    public function test_alvarez_queda_en_bloque_a_antes_de_z(): void
    {
        $filas = [
            ['id_nom' => 10, 'ap_nom' => 'Zapata, Ana', 'ap1' => 'Zapata', 'ap2' => '', 'nom' => 'Ana'],
            ['id_nom' => 20, 'ap_nom' => 'Álvarez, Pedro', 'ap1' => 'Álvarez', 'ap2' => '', 'nom' => 'Pedro'],
            ['id_nom' => 30, 'ap_nom' => 'Amador, Luis', 'ap1' => 'Amador', 'ap2' => '', 'nom' => 'Luis'],
        ];
        \src\shared\domain\helpers\FuncTablasSupport::usortProfesoresPorApellidos($filas);

        $this->assertSame(
            ['Álvarez, Pedro', 'Amador, Luis', 'Zapata, Ana'],
            array_column($filas, 'ap_nom'),
        );
    }
}
