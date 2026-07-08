<?php

declare(strict_types=1);

namespace Tests\unit\frontend\web;

use frontend\shared\security\HashFront;
use frontend\shared\web\NavStack;
use PHPUnit\Framework\TestCase;

/**
 * Escenarios de aceptación §8 sobre {@see NavStack} (Fase 0 — sin pantallas migradas).
 */
class NavStackTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('navstack-test-' . md5(static::class . (string) $this->name()));
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

    /* -----------------------------------------------------------------
     * E1 — bucle hermanos (actividades → asistentes → form A/B → atrás)
     * ---------------------------------------------------------------- */

    public function testE1SiblingLoopTruncatesIntermediateFormsOnReturnToList(): void
    {
        $nav = $this->nav();

        $nav->enter('/actividad_que.php', '#main', [], ['filtro' => 'x']);
        $nav->enter('/actividad_select.php', '#main', [], ['id_sel' => '10']);
        $nav->enter('/lista_asistentes.php', '#main', ['id_activ' => 5], ['sel' => 'a']);

        $nav->enter('/form_asistencia.php', '#main', ['id_asistente' => 1], ['campo' => 'A']);
        $this->assertSame(4, count($this->stackEntries()));

        $nav->enter('/lista_asistentes.php', '#main', ['id_activ' => 5], ['sel' => 'a']);
        $this->assertSame(3, count($this->stackEntries()));
        $this->assertSame('/lista_asistentes.php', $this->stackEntries()[2]['url'] ?? null);

        $nav->enter('/form_asistencia.php', '#main', ['id_asistente' => 2], ['campo' => 'B']);
        $nav->enter('/lista_asistentes.php', '#main', ['id_activ' => 5], ['sel' => 'a']);

        $stack = $this->stackEntries();
        $this->assertSame(3, count($stack));
        $this->assertSame('/lista_asistentes.php', $stack[2]['url']);

        $back = $nav->backTarget(1);
        $this->assertNotNull($back);
        $this->assertSame('/actividad_select.php', $back['url']);
    }

    /* -----------------------------------------------------------------
     * E2 — estado modificado en listado
     * ---------------------------------------------------------------- */

    public function testE2UpdateStatePersistsSelectionOnCurrentEntry(): void
    {
        $nav = $this->nav();
        $nav->enter('/actividad_select.php', '#main', [], ['id_sel' => '1']);
        $nav->updateState(['id_sel' => '42', 'scroll_id' => 'row-42']);

        $current = $nav->peek(0);
        $this->assertNotNull($current);
        $this->assertSame('42', $current['state']['id_sel']);
        $this->assertSame('row-42', $current['state']['scroll_id']);

        $back = $nav->backTarget(1);
        $this->assertNull($back);
    }

    public function testE2UpdateStateAtParentEntry(): void
    {
        $nav = $this->nav();
        $nav->enter('/actividad_que.php', '#main', [], []);
        $nav->enter('/actividad_select.php', '#main', [], ['id_sel' => '1']);
        $nav->updateStateAt(1, ['filtro' => 'nuevo']);

        $parent = $nav->peek(1);
        $this->assertNotNull($parent);
        $this->assertSame('nuevo', $parent['state']['filtro']);
    }

    /* -----------------------------------------------------------------
     * E3 — recarga misma página no crece la pila
     * ---------------------------------------------------------------- */

    public function testE3SamePageKeyReplacesEntryWithoutGrowingStack(): void
    {
        $nav = $this->nav();
        $nav->enter('/actividad_select.php', '#main', [], ['filtro' => 'a']);
        $nav->enter('/actividad_select.php', '#main', [], ['filtro' => 'b']);

        $stack = $this->stackEntries();
        $this->assertSame(1, count($stack));
        $this->assertSame('b', $stack[0]['state']['filtro']);
    }

    /* -----------------------------------------------------------------
     * E4 — menú reset
     * ---------------------------------------------------------------- */

    public function testE4NavResetClearsStackBeforeEnter(): void
    {
        $nav = $this->nav();
        $nav->enter('/actividad_que.php', '#main', [], []);
        $nav->enter('/actividad_select.php', '#main', [], []);

        $navReset = $this->nav(['nav' => 'reset']);
        $navReset->enter('/personas_que.php', '#main', [], []);

        $stack = $this->stackEntries();
        $this->assertSame(1, count($stack));
        $this->assertSame('/personas_que.php', $stack[0]['url']);
        $this->assertNull($navReset->backTarget(1));
    }

    public function testResetMethodEmptiesStack(): void
    {
        $nav = $this->nav();
        $nav->enter('/a.php', '#main', [], []);
        $nav->reset();
        $this->assertSame([], $this->stackEntries());
    }

    /* -----------------------------------------------------------------
     * E5 — profundidad que → select → hijo → ficha
     * ---------------------------------------------------------------- */

    public function testE5DeepNavigationBackTargetsChain(): void
    {
        $nav = $this->nav();
        $nav->enter('/actividad_que.php', '#main', [], ['q' => '1']);
        $nav->enter('/actividad_select.php', '#main', [], ['id_sel' => '7']);
        $nav->enter('/lista_asistentes.php', '#main', ['id_activ' => 3], []);
        $nav->enter('/form_asistencia.php', '#main', ['id_asistente' => 9], []);

        $this->assertSame('/lista_asistentes.php', $nav->backTarget(1)['url'] ?? null);
        $this->assertSame('/actividad_select.php', $nav->backTarget(2)['url'] ?? null);
        $this->assertSame('/actividad_que.php', $nav->backTarget(3)['url'] ?? null);
        $this->assertNull($nav->backTarget(4));
    }

    /* -----------------------------------------------------------------
     * E6 — hash válido en backTarget
     * ---------------------------------------------------------------- */

    public function testE6BackTargetProducesHashThatValidatePostAccepts(): void
    {
        $nav = $this->nav();
        $nav->enter('/actividad_que.php', '#main', [], ['filtro' => 'z']);
        $nav->enter('/actividad_select.php', '#main', [], ['id_sel' => '5']);

        $target = $nav->backTarget(1);
        $this->assertNotNull($target);

        parse_str($target['parametros'], $post);
        $this->assertIsArray($post);
        $this->assertArrayHasKey('h', $post);
        $this->assertSame('1', $post['hpos'] ?? null);
        $this->assertSame('z', $post['filtro'] ?? null);

        $rebuilt = HashFront::add_hash(
            ['filtro' => 'z'],
            $target['url'],
        );
        parse_str($rebuilt, $rebuiltPost);
        $this->assertSame($rebuiltPost['h'] ?? null, $post['h']);
    }

    /* -----------------------------------------------------------------
     * E7 — operaciones encadenadas no corrompen la pila
     * ---------------------------------------------------------------- */

    public function testE7SequentialMutationsKeepConsistentStack(): void
    {
        $nav = $this->nav();
        $nav->enter('/lista_asistentes.php', '#main', ['id_activ' => 1], ['id_sel' => '1']);
        $nav->updateState(['id_sel' => '2']);
        $nav->enter('/form_asistencia.php', '#main', ['id_asistente' => 10], []);
        $nav->updateState(['nota' => 'x']);
        $nav->enter('/lista_asistentes.php', '#main', ['id_activ' => 1], ['id_sel' => '2']);

        $stack = $this->stackEntries();
        $this->assertSame(1, count($stack));
        $this->assertSame('/lista_asistentes.php', $stack[0]['url']);
        $this->assertSame('2', $stack[0]['state']['id_sel']);
    }

    /* -----------------------------------------------------------------
     * Infraestructura
     * ---------------------------------------------------------------- */

    public function testEphemeralFieldsStrippedFromStateOnEnter(): void
    {
        $nav = $this->nav();
        $nav->enter('/x.php', '#main', [], [
            'id_sel' => '1',
            'h' => 'fake',
            'stack' => '99',
            'PHPSESSID' => 'sid',
        ]);

        $entry = $nav->peek(0);
        $this->assertNotNull($entry);
        $this->assertSame(['id_sel' => '1'], $entry['state']);
    }

    public function testStackTrimmedToMaxTwentyEntries(): void
    {
        $nav = $this->nav();
        for ($i = 0; $i < 25; $i++) {
            $nav->enter('/page_' . $i . '.php', '#main', ['i' => $i], []);
        }

        $this->assertSame(20, count($this->stackEntries()));
        $this->assertSame('/page_5.php', $this->stackEntries()[0]['url']);
        $this->assertSame('/page_24.php', $this->stackEntries()[19]['url']);
    }

    public function testPageKeyIsStableForSameIdentity(): void
    {
        $k1 = NavStack::pageKey('/a.php', ['b' => 2, 'a' => 1]);
        $k2 = NavStack::pageKey('/a.php', ['a' => 1, 'b' => 2]);
        $this->assertSame($k1, $k2);
    }

    public function testBackTargetAppendsUiDynamicFieldsOutsideHash(): void
    {
        $nav = $this->nav();
        $nav->enter('/actividad_select.php', '#main', [], ['id_sel' => '99', 'scroll_id' => '12']);
        $nav->enter('/lista_asistentes.php', '#main', ['id_activ' => 1], []);

        $target = $nav->backTarget(1);
        $this->assertNotNull($target);
        parse_str($target['parametros'], $post);
        $this->assertSame('99', $post['id_sel'] ?? null);
        $this->assertSame('12', $post['scroll_id'] ?? null);
    }

    public function testNormalizeBloqueAddsHashPrefix(): void
    {
        $this->assertSame('#main', NavStack::normalizeBloque('main'));
        $this->assertSame('#ficha3101', NavStack::normalizeBloque('#ficha3101'));
    }

    public function testBackTargetReadsSessionWithoutRestartAfterWriteClose(): void
    {
        session_start();
        $_SESSION = [];

        $nav = $this->nav();
        $nav->enter('/frontend/actividadestudios/controller/dossiers_ver.php', '#main', ['id' => 1], []);
        $nav->enter('/frontend/actividadestudios/controller/acta_notas.php', '#main', [
            'id_activ' => 42,
            'id_asignatura' => 1101,
        ], ['id_activ' => 42, 'id_asignatura' => 1101]);

        session_write_close();

        ob_start();
        echo 'partial output';

        try {
            $target = $nav->backTarget(1);
            $this->assertNotNull($target);
            $this->assertSame('/frontend/actividadestudios/controller/dossiers_ver.php', $target['url'] ?? null);
            $this->assertSame(PHP_SESSION_NONE, session_status());
        } finally {
            ob_end_clean();
        }
    }
}
