<?php

declare(strict_types=1);

namespace Tests\integration\pasarela\infrastructure\controllers;

use src\shared\infrastructure\InProcessSrcDispatch;
use Tests\myTest;

/** `/src/pasarela/exportar_que_actividad_tipo_data` responde con la configuración del selector de tipo de actividad (`actividad_tipo`); el HTML lo pinta el frontend. */
final class ExportarQueActividadTipoDataControllerTest extends myTest
{
    private const CONTROLADOR = __DIR__ . '/../../../../../src/pasarela/infrastructure/ui/http/controllers/exportar_que_actividad_tipo_data.php';

    public function test_devuelve_la_configuracion_del_selector(): void
    {
        $postAnterior = $_POST;
        $_POST = ['id_tipo_activ' => '131001', 'sasistentes' => 'n', 'sactividad' => 'crt'];
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::CONTROLADOR;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
            $_POST = $postAnterior;
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, substr($salida, 0, 200));
        $this->assertTrue($json['success']);
        $data = is_string($json['data']) ? json_decode($json['data'], true) : $json['data'];

        $this->assertSame(['131001', 'n', 'crt'], [$data['actividad_tipo']['id_tipo_activ'], $data['actividad_tipo']['asistentes'], $data['actividad_tipo']['actividad']]);
    }
}
