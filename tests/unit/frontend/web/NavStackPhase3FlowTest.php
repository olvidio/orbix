<?php

declare(strict_types=1);

namespace Tests\unit\frontend\web;

use frontend\shared\web\NavStack;
use PHPUnit\Framework\TestCase;

/**
 * Flujo Fase 3 (impresiones) sobre {@see NavStack}.
 */
class NavStackPhase3FlowTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('navstack-phase3-' . md5(static::class . (string) $this->name()));
        session_start();
        $_SESSION = [];
        session_write_close();
    }

    private function nav(array $request = []): NavStack
    {
        return new NavStack($request);
    }

    /** @return list<array<string, mixed>> */
    private function stackEntries(): array
    {
        session_start();
        /** @var list<array<string, mixed>> $stack */
        $stack = $_SESSION['nav']['stack'] ?? [];
        session_write_close();

        return $stack;
    }

    public function testActaImprimirBackTargetRestoresActaNotasParent(): void
    {
        $nav = $this->nav();
        $parentState = [
            'id_activ' => 42,
            'id_asignatura' => 1101,
            'id_pau' => 42,
            'sel' => ['42#1101'],
        ];
        $nav->enter('/frontend/actividadestudios/controller/acta_notas.php', '#main', [
            'id_activ' => 42,
            'id_asignatura' => 1101,
        ], $parentState);

        $nav->enter('/frontend/notas/controller/acta_imprimir.php', '#main', ['acta' => 'A-2026-001'], [
            'acta' => 'A-2026-001',
            'cara' => 'A',
        ]);

        $this->assertSame(2, count($this->stackEntries()));
        $target = $nav->backTarget(1);
        $this->assertNotNull($target);
        $this->assertSame('/frontend/actividadestudios/controller/acta_notas.php', $target['url'] ?? null);
        $this->assertStringContainsString('id_activ=42', $target['parametros'] ?? '');
        $this->assertStringContainsString('id_asignatura=1101', $target['parametros'] ?? '');
    }

    public function testActaImprimirCaraSwitchDoesNotGrowStack(): void
    {
        $nav = $this->nav();
        $nav->enter('/frontend/actividadestudios/controller/acta_notas.php', '#main', [
            'id_activ' => 42,
            'id_asignatura' => 1101,
        ], ['id_activ' => 42, 'id_asignatura' => 1101]);

        $nav->enter('/frontend/notas/controller/acta_imprimir.php', '#main', ['acta' => 'A-2026-001'], [
            'acta' => 'A-2026-001',
            'cara' => 'A',
        ]);
        $nav->enter('/frontend/notas/controller/acta_imprimir.php', '#main', ['acta' => 'A-2026-001'], [
            'acta' => 'A-2026-001',
            'cara' => 'B',
        ]);

        $this->assertSame(2, count($this->stackEntries()));
        $top = $this->stackEntries()[1];
        $this->assertSame('B', $top['state']['cara'] ?? null);
    }

    public function testTesseraImprimirBackTargetRestoresParent(): void
    {
        $nav = $this->nav();
        $nav->enter('/frontend/personas/controller/personas_select.php', '#main', [], [
            'id_sel' => '7#tabla1',
        ]);
        $nav->enter('/frontend/notas/controller/tessera_imprimir.php', '#main', [
            'id_nom' => 7,
            'id_tabla' => 'tabla1',
        ], [
            'id_nom' => 7,
            'id_tabla' => 'tabla1',
            'cara' => 'A',
        ]);

        $target = $nav->backTarget(1);
        $this->assertNotNull($target);
        $this->assertSame('/frontend/personas/controller/personas_select.php', $target['url'] ?? null);
    }

    public function testUpdateStateAtSyncsParentAfterPrintEnter(): void
    {
        $nav = $this->nav();
        $nav->enter('/frontend/actividadestudios/controller/acta_notas.php', '#main', [
            'id_activ' => 42,
            'id_asignatura' => 1101,
        ], ['id_activ' => 42, 'id_asignatura' => 1101]);

        $nav->enter('/frontend/notas/controller/acta_imprimir.php', '#main', ['acta' => 'X'], [
            'acta' => 'X',
            'cara' => 'A',
        ]);
        $nav->updateStateAt(1, [
            'id_activ' => 42,
            'id_asignatura' => 1101,
            'id_sel' => '42#1101',
            'scroll_id' => '3',
        ]);

        $target = $nav->backTarget(1);
        $this->assertNotNull($target);
        $this->assertStringContainsString('scroll_id=3', $target['parametros'] ?? '');
    }
}
