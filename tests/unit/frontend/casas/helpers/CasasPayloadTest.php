<?php

declare(strict_types=1);

namespace Tests\unit\frontend\casas\helpers;

use frontend\casas\helpers\CasasPayload;
use PHPUnit\Framework\TestCase;
use src\usuarios\domain\entity\Usuario;

final class CasasPayloadTest extends TestCase
{
    private mixed $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousSession === null) {
            unset($_SESSION);
        } else {
            $_SESSION = $this->previousSession;
        }
        parent::tearDown();
    }

    /**
     * Regresión: se comprobaba `method_exists($u, 'getCsv_id_pau')` (renombrado) y el filtro
     * por casa del usuario DMZ quedaba siempre vacío.
     */
    public function test_devuelve_csv_id_pau_del_usuario_de_sesion(): void
    {
        $usuario = new Usuario();
        $usuario->setCsvIdPauVo('30019174');
        $_SESSION['session_auth'] = ['MiUsuario' => $usuario];

        $this->assertSame('30019174', CasasPayload::miUsuarioCsvIdPau());
    }

    public function test_sin_usuario_en_sesion_devuelve_cadena_vacia(): void
    {
        $_SESSION['session_auth'] = [];

        $this->assertSame('', CasasPayload::miUsuarioCsvIdPau());
    }

    public function test_usuario_sin_id_pau_devuelve_cadena_vacia(): void
    {
        $_SESSION['session_auth'] = ['MiUsuario' => new Usuario()];

        $this->assertSame('', CasasPayload::miUsuarioCsvIdPau());
    }
}
