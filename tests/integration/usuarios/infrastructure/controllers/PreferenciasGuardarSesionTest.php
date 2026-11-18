<?php

declare(strict_types=1);

namespace Tests\integration\usuarios\infrastructure\controllers;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\entity\Preferencia;
use Tests\myTest;

/**
 * Al guardar las preferencias, el orden «nombre apellidos» / «apellidos, nombre»
 * tiene que verse en la petición siguiente, sin salir y volver a entrar.
 *
 * El navegador llega a `preferencias_guardar.php` por `src_ajax.php`, que hace
 * `FrontBootstrap::boot()` (cierra la sesión con `session_write_close()`) y
 * despacha el controlador en el mismo proceso: lo que escriba en `$_SESSION`
 * con la sesión cerrada no se guarda.
 */
final class PreferenciasGuardarSesionTest extends myTest
{
    private const CONTROLADOR = __DIR__ . '/../../../../../src/usuarios/infrastructure/ui/http/controllers/preferencias_guardar.php';
    private const TIPOS = ['layout', 'inicio', 'estilo', 'tabla_presentacion', 'ordenApellidos', 'idioma', 'zona_horaria'];

    private PreferenciaRepositoryInterface $repository;
    private int $idUsuario;
    /** @var array<string, ?Preferencia> */
    private array $preferenciasAntes = [];
    /** @var array<string, mixed> */
    private array $postAnterior = [];
    /** @var array<string, mixed> */
    private array $sesionAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->postAnterior = $_POST;
        $this->sesionAnterior = $_SESSION;
        $this->repository = $GLOBALS['container']->get(PreferenciaRepositoryInterface::class);
        $this->idUsuario = ConfigGlobal::mi_id_usuario();
        foreach (self::TIPOS as $tipo) {
            $this->preferenciasAntes[$tipo] = $this->repository->findById($this->idUsuario, $tipo);
        }
    }

    public function tearDown(): void
    {
        foreach ($this->preferenciasAntes as $tipo => $oPreferencia) {
            if ($oPreferencia !== null) {
                $this->repository->Guardar($oPreferencia);
                continue;
            }
            $oNueva = $this->repository->findById($this->idUsuario, $tipo);
            if ($oNueva !== null) {
                $this->repository->Eliminar($oNueva);
            }
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();
        }
        $_POST = $this->postAnterior;
        $_SESSION = $this->sesionAnterior;
        parent::tearDown();
    }

    public function test_el_orden_de_apellidos_guardado_vale_para_la_peticion_siguiente(): void
    {
        $actual = ConfigGlobal::mi_ordenApellidos();
        $nuevo = $actual === 'nom_ap' ? 'ap_nom' : 'nom_ap';

        // Sesión del usuario, cerrada como la deja FrontBootstrap::boot().
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('pref-guardar-' . md5((string) $this->name() . microtime()));
        session_start();
        $_SESSION = ['session_auth' => $this->sesionAnterior['session_auth']];
        session_write_close();
        // Tras session_write_close() $_SESSION sigue en memoria (el resto del
        // request la lee); se restaura entera para el bootstrap del controlador.
        $_SESSION = $this->sesionAnterior;

        $_POST = [
            'ctx_guardar' => HashB::sign('preferencias_guardar'),
            'layout' => '',
            'inicio' => '',
            'oficina' => '',
            'estilo_color' => '',
            'tipo_menu' => '',
            'tipo_tabla' => '',
            'ordenApellidos' => $nuevo,
            'idioma_nou' => '',
            'zona_horaria_nou' => '',
        ];
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::CONTROLADOR;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, 'debe responder JSON: ' . substr($salida, 0, 200));
        $this->assertTrue($json['success'], (string) ($json['mensaje'] ?? ''));

        // Petición siguiente: se lee la sesión guardada.
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_start();
        $this->assertSame($nuevo, ConfigGlobal::mi_ordenApellidos());
    }
}
