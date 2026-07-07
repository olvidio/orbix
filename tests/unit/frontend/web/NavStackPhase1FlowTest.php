<?php

declare(strict_types=1);

namespace Tests\unit\frontend\web;

use frontend\shared\helpers\ListNavSupport;
use frontend\shared\web\NavStack;
use PHPUnit\Framework\TestCase;

/**
 * Flujo piloto Fase 1 (actividades) sobre {@see NavStack} — escenarios E1–E5 del §8.
 */
class NavStackPhase1FlowTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('navstack-phase1-' . md5(static::class . (string) $this->name()));
        session_start();
        $_SESSION = [];
        session_write_close();
    }

    private function nav(array $request = []): NavStack
    {
        return new NavStack($request);
    }

    /** @param array<string, mixed> $state */
    private function enterQue(NavStack $nav, array $state): void
    {
        $nav->enter('/frontend/actividades/controller/actividad_que.php', '#main', [], $state);
    }

    /** @param array<string, mixed> $state */
    private function enterSelect(NavStack $nav, array $state): void
    {
        $nav->enter('/frontend/actividades/controller/actividad_select.php', '#main', [], $state);
        $nav->updateStateAt(1, ListNavSupport::buildActividadQueReturnParametros($state));
    }

    /** @param array<string, mixed> $state */
    private function enterListaAsistentes(NavStack $nav, int $idActiv, array $state): void
    {
        $nav->enter(
            '/frontend/asistentes/controller/lista_asistentes.php',
            '#main',
            ['id_activ' => $idActiv],
            $state,
        );
    }

    /** @param array<string, mixed> $state */
    private function enterFormAsistente(NavStack $nav, int $idActiv, int $idNom, array $state): void
    {
        $nav->enter(
            '/frontend/asistentes/controller/form_asistentes_a_una_actividad.php',
            '#ficha3101',
            ['id_activ' => $idActiv, 'id_nom' => $idNom],
            $state,
        );
    }

    /** @param array<string, mixed> $state */
    private function enterListaClasesCa(NavStack $nav, int $idActiv, array $state): void
    {
        $nav->enter(
            '/frontend/actividadestudios/controller/lista_clases_ca.php',
            '#main',
            ['id_activ' => $idActiv],
            $state,
        );
    }

    public function testE1ActividadesAsistentesFormLoopBackToSelect(): void
    {
        $nav = $this->nav();
        $filtros = ['modo' => 'buscar', 'que' => '', 'status' => 2, 'id_sel' => '42'];

        $this->enterQue($nav, $filtros);
        $this->enterSelect($nav, array_merge($filtros, ['id_sel' => '42']));
        $this->enterListaAsistentes($nav, 5, ['sel' => ['5#Actividad demo']]);
        $this->enterFormAsistente($nav, 5, 1, ['id_activ' => 5, 'id_nom' => 1]);
        $this->enterListaAsistentes($nav, 5, ['sel' => ['5#Actividad demo']]);
        $this->enterFormAsistente($nav, 5, 2, ['id_activ' => 5, 'id_nom' => 2]);
        $this->enterListaAsistentes($nav, 5, ['sel' => ['5#Actividad demo']]);

        $back = $nav->backTarget(1);
        $this->assertNotNull($back);
        $this->assertSame('/frontend/actividades/controller/actividad_select.php', $back['url']);

        $selectEntry = $nav->peek(1);
        $this->assertNotNull($selectEntry);
        $this->assertSame('42', $selectEntry['state']['id_sel'] ?? null);
    }

    public function testE3ResubmitSelectDoesNotGrowStack(): void
    {
        $nav = $this->nav();
        $this->enterQue($nav, ['modo' => 'buscar']);
        $this->enterSelect($nav, ['modo' => 'buscar', 'nom_activ' => 'a']);
        $this->enterSelect($nav, ['modo' => 'buscar', 'nom_activ' => 'b']);

        session_start();
        $count = count($_SESSION['nav']['stack'] ?? []);
        session_write_close();

        $this->assertSame(2, $count);
    }

    public function testE4MenuResetStartsFreshStack(): void
    {
        $nav = $this->nav();
        $this->enterQue($nav, ['modo' => 'buscar']);
        $this->enterSelect($nav, ['modo' => 'buscar']);

        $navReset = $this->nav(['nav' => 'reset']);
        $navReset->enter('/frontend/personas/controller/personas_que.php', '#main', [], []);

        $this->assertNull($navReset->backTarget(1));
    }

    public function testE5DeepBackChain(): void
    {
        $nav = $this->nav();
        $this->enterQue($nav, ['modo' => 'buscar', 'q' => '1']);
        $this->enterSelect($nav, ['modo' => 'buscar', 'id_sel' => '7']);
        $this->enterListaClasesCa($nav, 3, ['id_activ' => 3]);
        $this->enterFormAsistente($nav, 3, 9, ['id_activ' => 3, 'id_nom' => 9]);

        $this->assertSame('/frontend/actividadestudios/controller/lista_clases_ca.php', $nav->backTarget(1)['url'] ?? null);
        $this->assertSame('/frontend/actividades/controller/actividad_select.php', $nav->backTarget(2)['url'] ?? null);
        $this->assertSame('/frontend/actividades/controller/actividad_que.php', $nav->backTarget(3)['url'] ?? null);
    }
}
