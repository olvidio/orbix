<?php

declare(strict_types=1);

namespace Tests\unit\usuarios\application;

use src\usuarios\application\support\SesionAlEntrar;
use src\usuarios\domain\entity\Usuario;
use src\usuarios\infrastructure\security\EnlaceRecuperacionPassword;
use Tests\myTest;

/**
 * Lo que se guarda en la sesión al entrar (web y app móvil) y otras piezas del acceso
 * (revisión de seguridad del 2026-11-20, SES-09 y SES-11 a SES-14).
 */
final class SesionAlEntrarTest extends myTest
{
    private const RAIZ = __DIR__ . '/../../../..';

    private function fuente(string $ruta): string
    {
        return (string) file_get_contents(self::RAIZ . '/' . $ruta);
    }

    public function test_el_usuario_que_va_a_la_sesion_no_lleva_ni_la_contrasena_ni_el_secreto_2fa(): void
    {
        $u = (new Usuario())->setAllAttributes([
            'id_usuario' => 7, 'usuario' => 'pepe', 'id_role' => 3, 'password' => str_repeat('ab', 164),
            'secret_2fa' => 'JBSWY3DPEHPK3PXPJBSWY3DP', 'has_2fa' => true,
        ]);

        $s = SesionAlEntrar::usuarioSinCredenciales($u);

        $this->assertNull($s->getPasswordVo());
        $this->assertNull($s->getSecret2faVo());
        $this->assertSame(7, $s->getId_usuario());
        $this->assertSame(3, $s->getId_role());
        $this->assertStringNotContainsString('JBSWY3DPEHPK3PXP', serialize($s));
        $this->assertNotNull($u->getPasswordVo(), 'el original no se toca');
    }

    public function test_al_entrar_no_queda_nada_de_la_sesion_anterior(): void
    {
        $_SESSION = ['session_auth' => ['username' => 'otro'], 'iPermMenus' => 1048576, 'session_perms_hydrated' => true, 'nav' => [1]];

        SesionAlEntrar::iniciarLimpia();

        $this->assertArrayNotHasKey('iPermMenus', $_SESSION);
        $this->assertArrayNotHasKey('session_perms_hydrated', $_SESSION);
        $this->assertArrayNotHasKey('session_auth', $_SESSION);
        $this->assertArrayNotHasKey('nav', $_SESSION);
    }

    public function test_los_dos_logins_guardan_el_usuario_sin_credenciales_y_comparan_en_tiempo_constante(): void
    {
        foreach (['src/usuarios/application/LoginProcesar.php', 'src/usuarios/application/AppMobileLogin.php'] as $f) {
            $codigo = $this->fuente($f);
            $this->assertStringContainsString('SesionAlEntrar::usuarioSinCredenciales(', $codigo, $f);
            $this->assertStringContainsString('->verify($password, ', $codigo, $f);
            $this->assertDoesNotMatchRegularExpression('/->encode\([^)]*\)\s*!==/', $codigo, $f);
            $this->assertStringContainsString('->needsRehash(', $codigo, $f);
        }
        $this->assertStringContainsString('SesionAlEntrar::iniciarLimpia(', $this->fuente('src/usuarios/application/AppMobileLogin.php'));
    }

    public function test_el_nombre_de_usuario_va_como_argumento_escapado_al_medidor_de_contrasenas(): void
    {
        $codigo = $this->fuente('src/usuarios/domain/PasswordHasher.php');

        $this->assertStringContainsString('escapeshellarg($username)', $codigo);
        $this->assertDoesNotMatchRegularExpression('/shell_exec\([^;]*\)\s*\.\s*\' \'\s*\.\s*\$username/', $codigo);
    }

    public function test_sin_secreto_configurado_el_enlace_de_restablecer_no_vale(): void
    {
        $clave = 'ORBIX_SIGNED_DOWNLOAD_TOKEN_SECRET';
        $antesEnv = $_ENV[$clave] ?? null;
        $antes = getenv($clave);
        unset($_ENV[$clave]);
        putenv($clave);
        try {
            $tk = EnlaceRecuperacionPassword::emitir(7, 'H-dlbv', 'hash');
            $this->assertNull(EnlaceRecuperacionPassword::abrir($tk));
        } finally {
            if ($antesEnv !== null) {
                $_ENV[$clave] = $antesEnv;
            }
            if ($antes !== false) {
                putenv($clave . '=' . $antes);
            }
        }
    }

    public function test_la_ayuda_de_acceso_no_monta_enlaces_con_la_cabecera_host(): void
    {
        $this->assertStringNotContainsString('UrlBaseProject', $this->fuente('frontend/usuarios/controller/ayuda_acceso.php'));
    }

    public function test_la_clave_de_un_2fa_ya_activo_no_se_vuelve_a_ensenar_ni_se_sustituye(): void
    {
        $info = $this->fuente('src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_info.php');
        $this->assertStringContainsString("\$data['secret_2fa'] = \$has_2fa ? '' :", $info);

        $update = $this->fuente('src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_update.php');
        $this->assertStringContainsString('if ($Qenable_2fa && $oUsuario->isHas_2fa()) {', $update);

        $vista = $this->fuente('frontend/usuarios/view/usuario_form_2fa.phtml');
        $this->assertStringContainsString('<?php if (!$has_2fa): ?>', $vista);
    }
}
