<?php

declare(strict_types=1);

namespace Tests\unit\shared\application;

use PHPUnit\Framework\TestCase;
use src\shared\application\AyudaPreguntar;

final class AyudaPreguntarTest extends TestCase
{
    private string $docsRoot;

    protected function setUp(): void
    {
        $this->docsRoot = sys_get_temp_dir() . '/orbix_ayuda_' . bin2hex(random_bytes(4));
        mkdir($this->docsRoot . '/manual', 0777, true);
        mkdir($this->docsRoot . '/ai/notas/flujos', 0777, true);

        file_put_contents($this->docsRoot . '/manual/notas.md', <<<'MD'
---
tipo: "manual_usuario"
modulo: "notas"
---

# Manual De Usuario - notas

## Acta

Cómo crear un acta y añadir alumnos al listado.
MD);

        file_put_contents($this->docsRoot . '/ai/notas/flujos/acta_ver.md', <<<'MD'
---
tipo: "ayuda_ia"
titulo: "Acta Ver"
preguntas: ["Como añadir alumno al acta?", "Como ver notas del acta?"]
---

# Ayuda IA - Acta Ver

Pasos para añadir un alumno al acta desde el listado standalone.
MD);

        mkdir($this->docsRoot . '/ai/asistentes/flujos', 0777, true);
        file_put_contents($this->docsRoot . '/ai/asistentes/flujos/form_asistentes.md', <<<'MD'
---
tipo: "ayuda_ia"
titulo: "Anotar la asistencia de una persona a una actividad"
preguntas: ["Como anotar la asistencia de una persona a una actividad?"]
---

# Ayuda IA - Anotar la asistencia de una persona a una actividad

Usa este documento para responder preguntas de usuario.
MD);

        mkdir($this->docsRoot . '/guias/actividades', 0777, true);
        file_put_contents($this->docsRoot . '/guias/PLAN_GUIAS_PROCESOS.md', "# Plan\n\nApuntar asistente persona actividad matricular.\n");
        file_put_contents($this->docsRoot . '/guias/actividades/_borrador_interno.md', "# Interno\n\nApuntar asistente persona actividad.\n");
        file_put_contents($this->docsRoot . '/guias/actividades/gestionar_asistentes.md', <<<'MD'
---
tipo: guia_proceso
titulo: Gestionar asistentes de una actividad
area: actividades
modulos: [asistentes]
estado: revisada
preguntas: ["¿Quién puede cambiar los asistentes de una actividad?"]
---

# Gestionar asistentes de una actividad

## Qué vas a conseguir

Apuntar personas como asistentes de una actividad.

## Pasos

### 1. Apuntar un asistente nuevo

<!-- preguntas: "¿Cómo doy de alta a una persona en una actividad?" | "¿Cómo apunto a alguien a un curso?" -->

1. En **Relación de asistentes**, pulse **+ n**.
2. Elija la persona en el desplegable **Asistente**.
3. Pulse **Guardar datos del asistente**.

### 2. Matricular de una asignatura

1. Pulse **añadir asignatura**.
2. Pulse **Guardar**.

#### Si el alumno es de otra dl

Solo su delegación puede matricularle.

### 3. Consultar el planning

<!-- preguntas: "¿Dónde veo la ocupación?" -->

#### 3.1. Llegar al cuadro

Abra **planning** en el menú.

#### 3.2. Elegir el periodo

Elija el trimestre y pulse **ver**.

## Errores frecuentes

| Mensaje | Causa |
|---------|-------|
| debe indicar una actividad | falta la actividad |

---

## Para mantenimiento (técnico)

Endpoint zzinterno `/src/asistentes/asistente_update`.
MD);
    }

    protected function tearDown(): void
    {
        $this->rmTree($this->docsRoot);
    }

    public function test_pregunta_vacia(): void
    {
        $uc = new AyudaPreguntar($this->docsRoot);
        $r = $uc->execute([]);
        $this->assertSame([], $r['resultados']);
        $this->assertNotSame('', $r['respuesta']);
    }

    public function test_encuentra_acta_en_ai_y_manual(): void
    {
        $uc = new AyudaPreguntar($this->docsRoot);
        $r = $uc->execute(['pregunta' => 'cómo añadir alumno al acta']);
        $this->assertNotSame([], $r['resultados']);
        $fuentes = array_column($r['resultados'], 'fuente');
        $this->assertTrue(
            in_array('docs/ai/notas/flujos/acta_ver.md', $fuentes, true)
            || in_array('docs/manual/notas.md', $fuentes, true)
        );
        $this->assertSame('busqueda_local', $r['modo']);
        $this->assertStringContainsString('documentación local', mb_strtolower($r['respuesta']));
    }

    /** @return list<array<string, mixed>> */
    private function buscar(string $pregunta): array
    {
        return (new AyudaPreguntar($this->docsRoot))->execute(['pregunta' => $pregunta])['resultados'];
    }

    public function test_la_guia_de_proceso_responde_antes_que_la_ficha_generada(): void
    {
        $r = $this->buscar('como doy de alta a una persona a una actividad');
        $this->assertNotSame([], $r);
        $this->assertSame('guia', $r[0]['tipo']);
        $this->assertSame('docs/guias/actividades/gestionar_asistentes.md', $r[0]['fuente']);
        $this->assertSame('Gestionar asistentes de una actividad › 1. Apuntar un asistente nuevo', $r[0]['titulo']);
        $this->assertSame('actividades', $r[0]['modulo']);
    }

    public function test_el_resultado_de_una_guia_trae_los_pasos_completos_del_apartado(): void
    {
        $r = $this->buscar('como doy de alta a una persona a una actividad');
        $pasos = $r[0]['pasos'];
        $this->assertStringContainsString('1. En **Relación de asistentes**, pulse **+ n**.', $pasos);
        $this->assertStringContainsString('3. Pulse **Guardar datos del asistente**.', $pasos);
        $this->assertStringNotContainsString('preguntas:', $pasos);
        $this->assertStringNotContainsString('añadir asignatura', $pasos, 'solo el apartado, no el siguiente');
    }

    public function test_la_respuesta_lleva_los_pasos_de_la_guia_mejor_puntuada(): void
    {
        $r = (new AyudaPreguntar($this->docsRoot))->execute(['pregunta' => 'como doy de alta a una persona a una actividad']);
        $this->assertStringContainsString('Gestionar asistentes de una actividad › 1. Apuntar un asistente nuevo', $r['respuesta']);
        $this->assertStringContainsString('Pulse Guardar datos del asistente', $r['respuesta']);
    }

    public function test_encuentra_aunque_el_verbo_este_conjugado(): void
    {
        $r = $this->buscar('como matriculo de una asignatura');
        $this->assertSame('Gestionar asistentes de una actividad › 2. Matricular de una asignatura', $r[0]['titulo']);
    }

    public function test_un_subapartado_es_un_resultado_propio(): void
    {
        $r = $this->buscar('matricular a un alumno de otra dl');
        $this->assertSame(
            'Gestionar asistentes de una actividad › 2. Matricular de una asignatura › Si el alumno es de otra dl',
            $r[0]['titulo']
        );
    }

    public function test_un_apartado_trae_tambien_los_pasos_de_sus_subapartados(): void
    {
        $r = $this->buscar('donde veo la ocupacion');
        $this->assertSame('Gestionar asistentes de una actividad › 3. Consultar el planning', $r[0]['titulo']);
        $this->assertStringContainsString('**3.1. Llegar al cuadro**', $r[0]['pasos']);
        $this->assertStringContainsString('Abra **planning** en el menú.', $r[0]['pasos']);
        $this->assertStringContainsString('Elija el trimestre y pulse **ver**.', $r[0]['pasos']);
    }

    public function test_las_preguntas_de_la_cabecera_valen_para_la_presentacion_de_la_guia(): void
    {
        $r = $this->buscar('quien puede cambiar los asistentes');
        $this->assertSame('Gestionar asistentes de una actividad', $r[0]['titulo']);
        $this->assertStringContainsString('Apuntar personas como asistentes', $r[0]['pasos']);
    }

    public function test_los_errores_frecuentes_de_la_guia_se_encuentran(): void
    {
        $r = $this->buscar('debe indicar una actividad');
        $this->assertSame('Gestionar asistentes de una actividad › Errores frecuentes', $r[0]['titulo']);
    }

    public function test_no_busca_en_la_parte_tecnica_ni_en_los_ficheros_que_no_son_guias(): void
    {
        $this->assertSame([], $this->buscar('zzinterno'));
        foreach ($this->buscar('apuntar asistente persona actividad') as $hit) {
            $this->assertStringNotContainsString('PLAN_GUIAS', $hit['fuente']);
            $this->assertStringNotContainsString('_borrador_interno', $hit['fuente']);
        }
    }

    public function test_los_resultados_que_no_son_guias_llevan_pasos_vacios(): void
    {
        $otros = array_filter($this->buscar('ver notas del acta'), static fn (array $hit): bool => $hit['tipo'] !== 'guia');
        $this->assertNotSame([], $otros);
        foreach ($otros as $hit) {
            $this->assertSame('', $hit['pasos']);
        }
    }

    private function rmTree(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $f) {
            $f->isDir() ? rmdir($f->getPathname()) : unlink($f->getPathname());
        }
        rmdir($dir);
    }
}
