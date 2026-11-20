<?php

declare(strict_types=1);

namespace Tests\unit\frontend\shared;

use frontend\shared\PostRequest;
use PHPUnit\Framework\TestCase;

/**
 * El mensaje de error de un endpoint se imprime en la página: es texto. Puede traer el valor recibido o el
 * nombre de un registro, así que se escapa; solo se respetan los saltos `<br>` que ponen los propios
 * mensajes (revisión de seguridad del 2026-11-20, XSS-07).
 */
final class PostRequestMensajeSeguroTest extends TestCase
{
    public function test_el_mensaje_es_texto_y_conserva_sus_saltos(): void
    {
        $this->assertSame(
            'No encuentro: &lt;img src=x onerror=alert(1)&gt;<br>otra línea<br>y &amp; más',
            PostRequest::mensajeSeguro('No encuentro: <img src=x onerror=alert(1)><br>otra línea<br />y &amp; más')
        );
        $this->assertSame('L&#039;actividad &quot;x&quot;', PostRequest::mensajeSeguro("L'actividad \"x\""));
    }

    public function test_los_dos_caminos_de_error_lo_usan(): void
    {
        $codigo = (string) file_get_contents(__DIR__ . '/../../../../frontend/shared/PostRequest.php');

        $this->assertStringNotContainsString('$e->getMessage() . self::sufijoDiagnosticoLlamadaInterna', $codigo);
        $this->assertStringNotContainsString('$mensajeStr . self::sufijoDiagnosticoLlamadaInterna', $codigo);
    }
}
