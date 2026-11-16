<?php

declare(strict_types=1);

namespace Tests\integration\planning\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\infrastructure\InProcessSrcDispatch;
use Tests\myTest;

/**
 * `planning_ctr_select_data` y `planning_persona_ver_data` calculan el rango con `Periodo`;
 * los periodos de curso necesitan el calendario escolar de configuración.
 */
final class PlanningDataPeriodoControllersTest extends myTest
{
    private const DIR = __DIR__ . '/../../../../../src/planning/infrastructure/ui/http/controllers/';

    /** @return array<string, array{string, array<string, string>}> */
    public static function casos(): array
    {
        $centros = ['tipo' => 'planning_ctr', 'sel' => ''];
        $personas = ['sSeleccionados' => ''];

        return [
            'centros, año entero' => ['planning_ctr_select_data.php', $centros + ['periodo' => 'tot_any', 'year' => '2026']],
            'centros, curso crt' => ['planning_ctr_select_data.php', $centros + ['periodo' => 'curso_crt', 'year' => '2026']],
            'personas, curso ca' => ['planning_persona_ver_data.php', $personas + ['periodo' => 'curso_ca', 'year' => '2026']],
        ];
    }

    /** @param array<string, string> $post */
    #[DataProvider('casos')]
    public function test_responde_sin_error_con_el_periodo(string $controlador, array $post): void
    {
        $json = $this->llamar($controlador, $post);

        $this->assertTrue($json['success'], (string) ($json['mensaje'] ?? ''));
    }

    public function test_periodo_sin_fechas_avisa(): void
    {
        $json = $this->llamar('planning_ctr_select_data.php', [
            'tipo' => 'planning_ctr',
            'periodo' => 'otro',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertFalse($json['success']);
        $this->assertStringContainsString('Faltan fechas de periodo', (string) $json['mensaje']);
    }

    /**
     * @param array<string, string> $post
     * @return array<string, mixed>
     */
    private function llamar(string $controlador, array $post): array
    {
        $postAnterior = $_POST;
        $_POST = $post;
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::DIR . $controlador;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
            $_POST = $postAnterior;
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, substr($salida, 0, 300));

        return $json;
    }
}
