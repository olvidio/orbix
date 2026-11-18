<?php

declare(strict_types=1);

namespace Tests\unit\usuarios\domain;

use PHPUnit\Framework\TestCase;
use src\usuarios\domain\Verify2fa;

/** Vectores del RFC 6238 (SHA-1, clave «12345678901234567890» = GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ). */
final class Verify2faTest extends TestCase
{
    private const SECRETO = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';

    private function codigo(int $paso): string
    {
        $m = new \ReflectionMethod(Verify2fa::class, 'calculate_totp');

        return $m->invoke(null, self::SECRETO, $paso);
    }

    public function test_codigos_del_rfc_6238(): void
    {
        $this->assertSame('287082', $this->codigo(intdiv(59, 30)));
        $this->assertSame('081804', $this->codigo(intdiv(1111111109, 30)));
        $this->assertSame('050471', $this->codigo(intdiv(1111111111, 30)));
    }

    /** Devuelve el paso de 30 s que corresponde al código (actual, anterior o siguiente), o null. */
    public function test_paso_del_codigo_en_la_ventana_aceptada(): void
    {
        $ahora = intdiv(time(), 30);
        foreach ([-1, 0, 1] as $desfase) {
            $paso = Verify2fa::paso_2fa_valido($this->codigo($ahora + $desfase), self::SECRETO);
            // En el cambio de ventana el paso puede correrse uno.
            $this->assertNotNull($paso);
            $this->assertLessThanOrEqual(1, abs($paso - ($ahora + $desfase)));
        }
        $this->assertTrue(Verify2fa::verify_2fa_code(' ' . $this->codigo($ahora) . ' ', self::SECRETO));
    }

    public function test_codigo_fuera_de_la_ventana_no_vale(): void
    {
        $viejo = $this->codigo(intdiv(time(), 30) - 10);
        $validos = array_map(fn (int $d): string => $this->codigo(intdiv(time(), 30) + $d), [-1, 0, 1]);
        if (in_array($viejo, $validos, true)) {
            $this->markTestSkipped('coincidencia improbable del código viejo con uno vigente');
        }

        $this->assertNull(Verify2fa::paso_2fa_valido($viejo, self::SECRETO));
        $this->assertFalse(Verify2fa::verify_2fa_code($viejo, self::SECRETO));
    }
}
