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

    public function testDossiersRefreshWithEphemeralModDoesNotGrowStack(): void
    {
        $nav = $this->nav();
        $identity = ['pau' => 'a', 'id_pau' => 42, 'obj_pau' => 'Persona', 'queSel' => 'matriculas', 'id_dossier' => '1303'];
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#ficha1303', $identity, $identity);

        $nav->enter(
            '/frontend/dossiers/controller/dossiers_ver.php',
            '#ficha1303',
            ListNavSupport::buildDossiersVerNavIdentity(array_merge($identity, ['mod' => 'sel_es_asistente', 'refresh' => '1'])),
            array_merge($identity, ['mod' => 'sel_es_asistente', 'refresh' => '1']),
        );

        $this->assertSame(1, count($this->stackEntries()));
    }

    public function testEnterOrRefreshDossiersVerRefreshUpdatesTopWithoutGrowingStack(): void
    {
        $_POST = [
            'pau' => 'a',
            'id_pau' => '42',
            'obj_pau' => 'Persona',
            'queSel' => 'matriculas',
            'id_dossier' => '1303',
            'refresh' => '1',
        ];
        $nav = $this->nav($_POST);
        $identity = ['pau' => 'a', 'id_pau' => 42, 'obj_pau' => 'Persona', 'queSel' => 'matriculas', 'id_dossier' => '1303'];
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#ficha1303', $identity, $identity);

        $oPosicion = new \frontend\shared\web\Posicion('', $_POST);
        ListNavSupport::enterOrRefreshDossiersVer($oPosicion);

        $this->assertSame(1, count($this->stackEntries()));
        unset($_POST);
    }

    public function testRefreshOnDossiersTopNeverGrowsStackEvenIfSegmentDiffers(): void
    {
        $asis = ['id_pau' => 42, 'id_dossier' => '3101', 'queSel' => 'asis'];
        $_POST = ['refresh' => '1', 'pau' => 'p', 'id_pau' => '99'];
        $nav = $this->nav($_POST);
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#main', $asis, $asis);

        $oPosicion = new \frontend\shared\web\Posicion('', $_POST);
        ListNavSupport::enterOrRefreshDossiersVer($oPosicion);

        $this->assertSame(1, count($this->stackEntries()));
        unset($_POST);
    }

    public function testForwardEnterUsesMainBloqueForDossiersVer(): void
    {
        $_POST = [
            'pau' => 'p',
            'id_pau' => '42',
            'obj_pau' => 'Persona',
            'queSel' => 'matriculas',
            'id_dossier' => '1303',
        ];
        $oPosicion = new \frontend\shared\web\Posicion('', $_POST);
        ListNavSupport::enterOrRefreshDossiersVer($oPosicion);

        $entry = $this->stackEntries()[0] ?? [];
        $this->assertSame('#main', $entry['bloque'] ?? null);
        unset($_POST);
    }

    public function testBackTargetForDossiersVerAlwaysUsesMainBloque(): void
    {
        $nav = $this->nav();
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#ficha3101', [
            'id_pau' => 42,
            'id_dossier' => '3101',
            'queSel' => 'asis',
        ], ['id_pau' => 42, 'id_dossier' => '3101', 'queSel' => 'asis']);
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#ficha1303', [
            'id_pau' => 42,
            'id_dossier' => '1303',
            'queSel' => 'matriculas',
        ], ['id_pau' => 42, 'id_dossier' => '1303', 'queSel' => 'matriculas']);

        $target = $nav->backTarget(1);
        $this->assertNotNull($target);
        $this->assertSame('#main', $target['bloque'] ?? null);
    }

    public function testPruneDuplicateDossiersVerSegmentsRemovesStaleLayer(): void
    {
        $asis = ['id_pau' => 42, 'id_dossier' => '3101', 'queSel' => 'asis'];
        $person = ['pau' => 'a', 'id_pau' => 42, 'obj_pau' => 'Persona', 'queSel' => 'matriculas', 'id_dossier' => '1303'];

        $nav = $this->nav();
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#main', $asis, $asis);
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#ficha1303', $person, $person);

        session_start();
        $_SESSION['nav']['stack'][] = [
            'key' => 'duplicate-person-plan',
            'url' => '/frontend/dossiers/controller/dossiers_ver.php',
            'bloque' => '#ficha1303',
            'identity' => $person,
            'state' => array_merge($person, ['refresh' => '1']),
            'ts' => time(),
        ];
        session_write_close();

        $this->assertSame(3, count($this->stackEntries()));

        $oPosicion = new \frontend\shared\web\Posicion('', []);
        ListNavSupport::pruneDuplicateDossiersVerSegments($oPosicion, $person);

        $this->assertSame(2, count($this->stackEntries()));
    }

    public function testNavBackStepsFromDossiersVerSkipsDuplicatePersonPlanLayers(): void
    {
        $asis = ['id_pau' => 42, 'id_dossier' => '3101', 'queSel' => 'asis'];
        $person = ['pau' => 'a', 'id_pau' => 42, 'obj_pau' => 'Persona', 'queSel' => 'matriculas', 'id_dossier' => '1303'];

        $_POST = array_merge($person, ['queSel' => 'matriculas', 'id_dossier' => '1303']);
        $nav = $this->nav($_POST);
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#main', $asis, $asis);
        $nav->enter('/frontend/dossiers/controller/dossiers_ver.php', '#ficha1303', $person, $person);

        session_start();
        $_SESSION['nav']['stack'][] = [
            'key' => 'duplicate-person-plan',
            'url' => '/frontend/dossiers/controller/dossiers_ver.php',
            'bloque' => '#ficha1303',
            'identity' => $person,
            'state' => array_merge($person, ['refresh' => '1']),
            'ts' => time(),
        ];
        session_write_close();

        $steps = ListNavSupport::navBackStepsFromDossiersVer($nav);
        $this->assertSame(2, $steps);

        $target = $nav->backTarget($steps);
        $this->assertNotNull($target);
        $this->assertStringContainsString('3101', $target['parametros'] ?? '');

        unset($_POST);
    }
}
