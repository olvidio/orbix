<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain;

use Tests\myTest;

/**
 * Test-guardia: en todas las entidades de src/<modulo>/domain/entity que declaran sus
 * campos para la interfaz (getDatosCampos()), el getter y el setter nombrados en
 * cada DatosCampo existen. Una errata en esos nombres rompe la pantalla genérica
 * de edición solo al abrirla.
 */
final class DatosCamposMetodosTest extends myTest
{
    public function test_los_metodos_nombrados_en_los_datos_de_campo_existen(): void
    {
        $raiz = dirname(__DIR__, 4);
        $errores = [];
        $revisadas = 0;
        foreach (glob($raiz . '/src/*/domain/entity/*.php') ?: [] as $fichero) {
            $rel = substr($fichero, strlen($raiz) + 1, -4);
            $clase = str_replace('/', '\\', $rel);
            if (!class_exists($clase) || !method_exists($clase, 'getDatosCampos')) {
                continue;
            }
            $ref = new \ReflectionClass($clase);
            if ($ref->isAbstract() || ($ref->getConstructor()?->getNumberOfRequiredParameters() ?? 0) > 0) {
                continue;
            }
            try {
                $campos = $ref->newInstance()->getDatosCampos();
            } catch (\Throwable $e) {
                $errores[] = "$clase::getDatosCampos() lanza " . get_class($e) . ': ' . $e->getMessage();
                continue;
            }
            $revisadas++;
            foreach ($campos as $campo) {
                foreach (['getMetodoGet', 'getMetodoSet'] as $quien) {
                    $metodo = method_exists($campo, $quien) ? (string) $campo->$quien() : '';
                    if ($metodo !== '' && !method_exists($clase, $metodo)) {
                        $errores[] = "$clase: el campo «{$campo->getNom_camp()}» apunta a $metodo(), que no existe";
                    }
                }
            }
        }
        $this->assertGreaterThan(20, $revisadas, 'se han revisado entidades');
        $this->assertSame([], $errores);
    }
}
