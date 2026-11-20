<?php

declare(strict_types=1);

namespace Tests\unit\frontend\security;

use frontend\shared\security\FirmadorE2e;
use PHPUnit\Framework\TestCase;

/**
 * El firmador de URL de la suite E2E da firmas `h` a la carta: solo responde en la instancia de pruebas
 * que tiene definido su secreto (`ORBIX_E2E_FIRMADOR`) y a quien lo presenta. Un entorno «pruebas» de una
 * instalación real no lo define y no lo ofrece (revisión de seguridad del 2026-11-20, CFG-05).
 */
final class FirmadorE2eTest extends TestCase
{
    public function test_solo_responde_en_pruebas_con_el_secreto_de_la_instancia(): void
    {
        $secreto = str_repeat('a', 32);

        $this->assertTrue(FirmadorE2e::permitido(true, $secreto, $secreto));
        $this->assertFalse(FirmadorE2e::permitido(false, $secreto, $secreto), 'fuera de la instancia de pruebas');
        $this->assertFalse(FirmadorE2e::permitido(true, $secreto, 'otro'));
        $this->assertFalse(FirmadorE2e::permitido(true, $secreto, ''));
        $this->assertFalse(FirmadorE2e::permitido(true, '', ''), 'sin secreto definido no existe');
        $this->assertFalse(FirmadorE2e::permitido(true, 'corto', 'corto'), 'un secreto corto no vale');
    }

    public function test_el_controlador_lo_comprueba(): void
    {
        $codigo = (string) file_get_contents(dirname(__DIR__, 4) . '/frontend/shared/controller/e2e_firmar.php');

        $this->assertStringContainsString('FirmadorE2e::permitido(', $codigo);
    }
}
