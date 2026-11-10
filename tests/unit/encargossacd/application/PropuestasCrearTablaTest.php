<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\PropuestasCrearTabla;

/**
 * El caso de uso admite inyectar la creación de tablas: así se prueba sin ejecutar DDL.
 */
final class PropuestasCrearTablaTest extends TestCase
{
    public function test_si_la_creacion_funciona_devuelve_exito(): void
    {
        $llamadas = 0;
        $uc = new PropuestasCrearTabla(function () use (&$llamadas): void {
            $llamadas++;
        });

        $this->assertSame(['success' => true], $uc->execute());
        $this->assertSame(1, $llamadas);
    }

    public function test_si_la_creacion_lanza_runtime_exception_devuelve_error_sin_propagarla(): void
    {
        $uc = new PropuestasCrearTabla(function (): void {
            throw new \RuntimeException('ya existe la tabla');
        });

        $out = $uc->execute();

        $this->assertFalse($out['success']);
        $this->assertNotSame('', $out['mensaje']);
        $this->assertStringNotContainsString('ya existe', $out['mensaje'], 'no se filtra el detalle técnico al usuario');
    }

    public function test_otras_excepciones_si_se_propagan(): void
    {
        $uc = new PropuestasCrearTabla(function (): void {
            throw new \LogicException('bug');
        });

        $this->expectException(\LogicException::class);
        $uc->execute();
    }
}
