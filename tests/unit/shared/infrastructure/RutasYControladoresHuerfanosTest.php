<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure;

use PHPUnit\Framework\TestCase;

/**
 * Una ruta apunta a un controlador que existe, y en `frontend/` no quedan controladores que solo incluyan
 * el de `src/` sin arrancar la aplicación: pedidos directamente no pasan por la sesión ni por la firma
 * (revisión de seguridad del 2026-11-20, LOG-11 y LOG-12).
 */
final class RutasYControladoresHuerfanosTest extends TestCase
{
    private function raiz(): string
    {
        return dirname(__DIR__, 4);
    }

    public function test_toda_ruta_apunta_a_un_controlador_que_existe(): void
    {
        $huerfanas = [];
        foreach ((array) glob($this->raiz() . '/src/*/config/routes.php') as $fichero) {
            $codigo = (string) preg_replace('~/\*.*?\*/~s', '', (string) file_get_contents((string) $fichero));
            preg_match_all("~require\\s+__DIR__\\s*\\.\\s*'([^']+)'~", $codigo, $m);
            foreach ($m[1] as $relativo) {
                if (!is_file(dirname((string) $fichero) . $relativo)) {
                    $huerfanas[] = substr((string) $fichero, strlen($this->raiz()) + 1) . ' → ' . $relativo;
                }
            }
        }

        $this->assertSame([], $huerfanas);
    }

    public function test_ningun_controlador_de_frontend_es_solo_un_require_del_de_src(): void
    {
        $sinArranque = [];
        foreach ((array) glob($this->raiz() . '/frontend/*/controller/*.php') as $fichero) {
            $codigo = (string) file_get_contents((string) $fichero);
            if (preg_match('~require(_once)?\s+[^;]*src/[^;]*/infrastructure/ui/http/controllers/~', $codigo) === 1
                && !str_contains($codigo, 'FrontBootstrap')
            ) {
                $sinArranque[] = substr((string) $fichero, strlen($this->raiz()) + 1);
            }
        }

        $this->assertSame([], $sinArranque);
    }

    public function test_el_cambio_de_fase_sin_ninguna_actividad_firmada_no_se_ejecuta(): void
    {
        $codigo = (string) file_get_contents($this->raiz() . '/src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_update.php');

        $this->assertMatchesRegularExpression('/if \(\$selRebuilt === \[\]\) \{\s*ContestarJson::enviar\(_\("Operación no autorizada"\)/', $codigo);
    }
}
