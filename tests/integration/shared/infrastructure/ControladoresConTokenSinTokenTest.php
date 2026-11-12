<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure;

use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\infrastructure\InProcessSrcDispatch;
use Tests\myTest;

/**
 * Test-guardia: todo controlador HTTP de `src/` que abre una cápsula `HashB`
 * rechaza una petición sin cápsula con JSON «Operación no autorizada» (y, como
 * la comprobación va antes, sin tocar datos ni cortar la petición con `exit`).
 *
 * Los que abren la cápsula solo en algunas ramas, opcional o por fila, solo
 * tienen que responder JSON (CAPSULA_NO_OBLIGATORIA, con el motivo).
 */
final class ControladoresConTokenSinTokenTest extends myTest
{
    private const RAIZ = __DIR__ . '/../../../../';

    /**
     * Abren la cápsula solo en algunas ramas, es opcional o va una por fila:
     * sin cápsula basta con que respondan JSON sin cortar la petición.
     * controlador relativo a src/ => motivo
     */
    private const CAPSULA_NO_OBLIGATORIA = [
        'usuarios/infrastructure/ui/http/controllers/preferencias_guardar.php' => 'el autoguardado de columnas de SlickGrid va sin cápsula (documentado en el controlador)',
        'encargossacd/infrastructure/ui/http/controllers/propuestas_ajax.php' => 'dispatcher: solo las acciones que escriben piden cápsula',
        'actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_form_data.php' => 'lectura: la cápsula es opcional (fija la tarifa a editar)',
        'menus/infrastructure/ui/http/controllers/menus_importar_de_ficheros_a_ref.php' => 'solo pide cápsula al ejecutar (seguro=1)',
        'cambios/infrastructure/ui/http/controllers/cambio_usuario_eliminar.php' => 'cápsula por fila: sin filas no borra nada',
        'procesos/infrastructure/ui/http/controllers/fases_activ_cambio_update.php' => 'cápsula por fila',
        'usuarios/infrastructure/ui/http/controllers/role_grupmenu_add.php' => 'cápsula por fila',
        'usuarios/infrastructure/ui/http/controllers/role_grupmenu_del.php' => 'cápsula por fila',
        'zonassacd/infrastructure/ui/http/controllers/zona_ctr_update.php' => 'cápsula por fila',
        'zonassacd/infrastructure/ui/http/controllers/zona_sacd_update.php' => 'cápsula por fila',
    ];

    /** Fuera del test. controlador => motivo */
    private const EXCLUIDOS = [
        'configuracion/infrastructure/ui/http/controllers/modulos_update.php' => 'responde texto; el proxy frontend lo adapta (AjaxJsonSupport::fromPlainText)',
    ];

    /** @return iterable<string, array{string}> */
    public static function controladoresProvider(): iterable
    {
        $raiz = realpath(self::RAIZ) . '/';
        foreach (glob($raiz . 'src/*/infrastructure/ui/http/controllers/*.php') ?: [] as $f) {
            $rel = substr($f, strlen($raiz . 'src/'));
            if (isset(self::EXCLUIDOS[$rel]) || !str_contains((string) file_get_contents($f), 'HashB::open(')) {
                continue;
            }
            yield $rel => [$f];
        }
    }

    #[DataProvider('controladoresProvider')]
    public function test_sin_capsula_no_autoriza(string $fichero): void
    {
        $postAnterior = $_POST;
        $getAnterior = $_GET;
        $_POST = [];
        $_GET = [];
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require $fichero;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
            $_POST = $postAnterior;
            $_GET = $getAnterior;
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, 'debe responder JSON; respondió: «' . substr($salida, 0, 200) . '»');
        $rel = substr((string) realpath($fichero), strlen((string) realpath(self::RAIZ)) + strlen('/src/'));
        if (isset(self::CAPSULA_NO_OBLIGATORIA[$rel])) {
            return;
        }
        $this->assertFalse($json['success'] ?? null);
        $this->assertStringContainsString('Operación no autorizada', (string) ($json['mensaje'] ?? ''));
    }
}
