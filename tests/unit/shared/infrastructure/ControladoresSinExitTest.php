<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure;

use Tests\myTest;

/**
 * Test-guardia: los controladores HTTP de `src/` no cortan la petición con
 * `exit`/`die` (en el despacho en proceso de PostRequest cortarían también al
 * controlador frontend que llama). Solo se permiten las descargas de binarios
 * y los casos anotados abajo con su motivo.
 */
final class ControladoresSinExitTest extends myTest
{
    /** controlador relativo a src/ => motivo */
    private const PERMITIDOS = [
        'certificados/infrastructure/ui/http/controllers/certificado_emitido_pdf_download.php' => 'descarga de binario',
        'certificados/infrastructure/ui/http/controllers/certificado_recibido_pdf_download.php' => 'descarga de binario',
        'notas/infrastructure/ui/http/controllers/acta_pdf_download.php' => 'descarga de binario',
        'usuarios/infrastructure/ui/http/controllers/check_first_login_2fa.php' => 'endpoint de navegación (redirige con header)',
        'menus/infrastructure/ui/http/controllers/menus_generar_txt.php' => 'herramienta de desarrollo que escribe un .phtml y responde texto',
        'menus/infrastructure/ui/http/controllers/menus_importar_de_ficheros_a_ref.php' => 'cierre $fail(): never tras ContestarJson',
    ];

    public function test_ningun_controlador_hace_exit(): void
    {
        $raiz = dirname(__DIR__, 4) . '/';
        $con = [];
        foreach (glob($raiz . 'src/*/infrastructure/ui/http/controllers/*.php') ?: [] as $f) {
            $rel = substr($f, strlen($raiz . 'src/'));
            if (isset(self::PERMITIDOS[$rel])) {
                continue;
            }
            foreach (\PhpToken::tokenize((string) file_get_contents($f)) as $t) {
                if ($t->id === T_EXIT) {
                    $con[] = "$rel:{$t->line}";
                }
            }
        }
        $this->assertSame([], $con);
    }
}
