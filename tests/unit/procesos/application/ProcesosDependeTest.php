<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\procesos\application\ProcesosDepende;

/**
 * Tests unitarios para el caso de uso ProcesosDepende centrados en el
 * camino de retorno temprano, donde no se debe interactuar con la BD.
 */
final class ProcesosDependeTest extends TestCase
{
    public function test_acc_vacio_devuelve_cadena_vacia(): void
    {
        $msg = (new ProcesosDepende())->execute([]);
        $this->assertSame('', $msg);
    }

    public function test_acc_no_valido_devuelve_cadena_vacia(): void
    {
        $msg = (new ProcesosDepende())->execute(['acc' => '#otro', 'valor_depende' => '5']);
        $this->assertSame('', $msg);
    }
}
