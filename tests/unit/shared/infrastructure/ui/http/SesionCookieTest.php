<?php

namespace Tests\unit\shared\infrastructure\ui\http;

use src\shared\infrastructure\ui\http\SesionCookie;
use Tests\myTest;

/**
 * La cookie de sesión lleva siempre HttpOnly y SameSite, también cuando PHP la reemite en una
 * petición que ya traía cookie; y Secure cuando la petición llega por HTTPS.
 */
class SesionCookieTest extends myTest
{
    public function test_los_atributos_no_dependen_de_que_sea_la_primera_visita(): void
    {
        $ini = SesionCookie::directivas([]);

        $this->assertSame('1', $ini['session.cookie_httponly']);
        $this->assertSame('Lax', $ini['session.cookie_samesite']);
        $this->assertSame('/', $ini['session.cookie_path']);
        $this->assertSame('1', $ini['session.use_only_cookies']);
        $this->assertSame('0', $ini['session.cookie_secure']);
    }

    public function test_por_https_la_cookie_es_secure(): void
    {
        $this->assertSame('1', SesionCookie::directivas(['HTTPS' => 'on'])['session.cookie_secure']);
        $this->assertSame('1', SesionCookie::directivas(['HTTP_X_FORWARDED_PROTO' => 'https'])['session.cookie_secure']);
        $this->assertSame('0', SesionCookie::directivas(['HTTPS' => 'off'])['session.cookie_secure']);
    }

    /**
     * Todo fichero que abre la sesión por primera vez en una petición configura antes la cookie.
     */
    public function test_los_arranques_de_sesion_configuran_la_cookie(): void
    {
        $raiz = dirname(__DIR__, 6);
        foreach (['index.php', 'public/index.php', 'src/shared/global_object.inc', 'frontend/shared/FrontBootstrap.php'] as $fichero) {
            $codigo = (string) file_get_contents($raiz . '/' . $fichero);
            $this->assertStringContainsString('SesionCookie::configurar()', $codigo, $fichero);
            $this->assertStringNotContainsString('session_set_cookie_params', $codigo, $fichero);
        }
    }

    /**
     * El identificador de sesión no se escribe en el HTML ni en el JavaScript, ni se acepta en el
     * cuerpo de la petición: solo viaja en la cookie.
     */
    public function test_el_id_de_sesion_no_se_imprime_ni_se_lee_del_post(): void
    {
        $raiz = dirname(__DIR__, 6);
        $malos = [];
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($raiz . '/frontend', \FilesystemIterator::SKIP_DOTS));
        $ficheros = [];
        foreach ($it as $f) {
            if (preg_match('/\.(phtml|twig)$/', $f->getFilename()) === 1) {
                $ficheros[] = $f->getPathname();
            }
        }
        $ficheros = array_merge($ficheros, glob($raiz . '/scripts/*.js') ?: [], glob($raiz . '/scripts/*.php') ?: []);
        foreach ($ficheros as $fichero) {
            if (preg_match('/session_id\s*\(\s*\)/', (string) file_get_contents($fichero)) === 1) {
                $malos[] = substr($fichero, strlen($raiz) + 1);
            }
        }
        $this->assertSame([], $malos, 'Imprimen session_id()');

        foreach (['frontend/shared/FrontBootstrap.php', 'frontend/shared/PostRequest.php'] as $fichero) {
            $codigo = (string) file_get_contents($raiz . '/' . $fichero);
            $this->assertDoesNotMatchRegularExpression('/\$_POST\[\$sessionName\]/', $codigo, $fichero);
        }
    }

    public function test_las_paginas_no_se_pueden_incrustar_desde_otro_sitio(): void
    {
        $cabeceras = SesionCookie::cabecerasDeSeguridad();

        $this->assertContains('X-Frame-Options: SAMEORIGIN', $cabeceras);
        $this->assertContains("Content-Security-Policy: frame-ancestors 'self'", $cabeceras);
    }

    public function test_las_respuestas_no_se_reinterpretan_ni_filtran_la_url(): void
    {
        $cabeceras = SesionCookie::cabecerasDeSeguridad();

        $this->assertContains('X-Content-Type-Options: nosniff', $cabeceras);
        $this->assertContains('Referrer-Policy: same-origin', $cabeceras);
        $this->assertSame([], preg_grep('/^Strict-Transport-Security/', $cabeceras), 'por http no se anuncia HSTS');
    }

    public function test_por_https_se_anuncia_hsts(): void
    {
        $this->assertContains('Strict-Transport-Security: max-age=31536000', SesionCookie::cabecerasDeSeguridad(['HTTPS' => 'on']));
    }
}
