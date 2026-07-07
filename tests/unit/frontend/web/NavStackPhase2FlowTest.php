<?php

declare(strict_types=1);

namespace Tests\unit\frontend\web;

use frontend\shared\helpers\ListNavSupport;
use frontend\shared\web\NavStack;
use PHPUnit\Framework\TestCase;

/**
 * Flujo Fase 2 (dossiers) sobre {@see NavStack}.
 */
class NavStackPhase2FlowTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('navstack-phase2-' . md5(static::class . (string) $this->name()));
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

    public function testDossiersChildBackTargetRestoresParentSegment(): void
    {
        $nav = $this->nav();
        $dossiersState = [
            'pau' => 'a',
            'obj_pau' => 'Actividad',
            'id_pau' => 42,
            'id_dossier' => '3102',
            'queSel' => 'carg',
        ];
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#main', [
            'id_pau' => 42,
            'id_dossier' => '3102',
            'queSel' => 'carg',
        ], $dossiersState);

        $nav->enter('/frontend/actividadcargos/controller/form_cargos_de_actividad.php', '#ficha3102', [
            'id_pau' => 42,
            'id_dossier' => '3102',
            'id_activ' => 42,
            'id_nom' => 7,
        ], array_merge($dossiersState, ['id_activ' => 42, 'id_nom' => 7, 'mod' => 'nuevo']));

        $this->assertSame(2, count($this->stackEntries()));
        $target = $nav->backTarget(1);
        $this->assertNotNull($target);
        $this->assertSame('/frontend/dossiers/controller/dossiers_ver.php', $target['url'] ?? null);
        $this->assertSame('#main', $target['bloque'] ?? null);
        $this->assertStringContainsString('queSel=carg', $target['parametros'] ?? '');
    }

    public function testBackStepsUntilUrlContainsFindsDossiersParent(): void
    {
        $nav = $this->nav();
        $nav->enter('/frontend/actividades/controller/actividad_select.php', '#main', [], ['id_sel' => '1']);
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#main', ['id_pau' => 5, 'queSel' => 'asis'], [
            'id_pau' => 5,
            'queSel' => 'asis',
            'id_dossier' => '3101',
        ]);
        $nav->enter('/frontend/actividadcargos/controller/form_cargos_de_actividad.php', '#ficha3102', [
            'id_pau' => 5,
            'id_nom' => 3,
        ], ['mod' => 'nuevo']);

        $this->assertSame(1, $nav->backStepsUntilUrlContains('dossiers_ver.php'));
    }

    public function testAsistentesDossierBackSkipsIntermediateDossierSegments(): void
    {
        $_POST = [
            'queSel' => 'asis',
            'id_dossier' => '3101',
            'id_pau' => '42',
        ];
        $nav = $this->nav($_POST);
        $nav->enter('/frontend/actividades/controller/actividad_que.php', '#main', [], []);
        $nav->enter('/frontend/actividades/controller/actividad_select.php', '#main', [], ['id_sel' => 'x']);
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#main', [
            'id_pau' => 42,
            'id_dossier' => '3005',
            'queSel' => 'asig',
        ], ['id_pau' => 42, 'id_dossier' => '3005', 'queSel' => 'asig']);
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#main', [
            'id_pau' => 42,
            'id_dossier' => '3101',
            'queSel' => 'asis',
        ], ['id_pau' => 42, 'id_dossier' => '3101', 'queSel' => 'asis']);

        $steps = ListNavSupport::navBackStepsFromDossiersVer($nav);
        $target = $nav->backTarget($steps);
        $this->assertNotNull($target);
        $this->assertSame('/frontend/actividades/controller/actividad_select.php', $target['url'] ?? null);
        unset($_POST);
    }

    public function testDossiersSegmentChangeUpdatesSameKeyWhenIdentityMatches(): void
    {
        $nav = $this->nav();
        $identity = ['id_pau' => 10, 'id_dossier' => '3101', 'queSel' => 'asis'];
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#main', $identity, ['scroll_id' => '1']);
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#main', $identity, ['scroll_id' => '5', 'id_sel' => 'abc']);

        $this->assertSame(1, count($this->stackEntries()));
        $state = $this->stackEntries()[0]['state'] ?? [];
        $this->assertSame('5', $state['scroll_id'] ?? null);
        $this->assertSame('abc', $state['id_sel'] ?? null);
    }
}
