<?php

declare(strict_types=1);

namespace Tests\unit\frontend\ubiscamas\helpers;

use frontend\ubiscamas\helpers\UbiscamasPayload;
use PHPUnit\Framework\TestCase;

final class UbiscamasPayloadTest extends TestCase
{
    private const CAMA_1 = '3f2a9c1e-5b7d-4e8a-9c01-0a1b2c3d4e5f';
    private const CAMA_2 = '7d1e2f3a-4b5c-4d6e-8f90-1a2b3c4d5e6f';
    private const CAMA_LIBRE = 'a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d';

    /**
     * Payload con la forma de /src/ubiscamas/actividad_habitaciones_lista:
     * los id de cama son UUID (CamaId) y `camas_con_asistentes` va indexado por ese UUID.
     *
     * @return array<string, mixed>
     */
    private static function payload(): array
    {
        return [
            'habitaciones_con_camas' => [
                'hab-1' => [
                    'habitacion' => ['nombre' => 'San José', 'planta' => '1'],
                    'camas' => [
                        ['id_cama' => self::CAMA_1],
                        ['id_cama' => self::CAMA_LIBRE],
                    ],
                ],
                'hab-2' => [
                    'habitacion' => ['nombre' => 'Santa María', 'planta' => '2'],
                    'camas' => [['id_cama' => self::CAMA_2]],
                ],
            ],
            'camas_con_asistentes' => [
                self::CAMA_1 => ['apellidos' => 'Zubiri, Ana', 'ctx_update_cama' => 'x'],
                self::CAMA_2 => ['apellidos' => 'Alonso, Juan', 'ctx_update_cama' => 'y'],
            ],
        ];
    }

    public function test_nombres_lista_empareja_asistentes_por_uuid_de_cama_y_ordena_por_nombre(): void
    {
        self::assertSame(
            [
                ['nombre' => 'Alonso, Juan', 'planta' => '2', 'habitacion' => 'Santa María'],
                ['nombre' => 'Zubiri, Ana', 'planta' => '1', 'habitacion' => 'San José'],
            ],
            UbiscamasPayload::nombresListaFromPayload(self::payload())
        );
    }

    /**
     * Con la preferencia «Nombre Apellidos» el texto empieza por el nombre de pila: el orden lo marca `orden`
     * (posición del asistente en el orden por apellidos del backend), no el texto.
     */
    public function test_nombres_lista_respeta_el_orden_del_backend_y_no_el_texto(): void
    {
        $data = self::payload();
        $data['camas_con_asistentes'] = [
            self::CAMA_1 => ['apellidos' => 'Ana Zubiri', 'orden' => 1, 'ctx_update_cama' => 'x'],
            self::CAMA_2 => ['apellidos' => 'Juan Alonso', 'orden' => 0, 'ctx_update_cama' => 'y'],
        ];

        self::assertSame(
            [
                ['nombre' => 'Juan Alonso', 'planta' => '2', 'habitacion' => 'Santa María'],
                ['nombre' => 'Ana Zubiri', 'planta' => '1', 'habitacion' => 'San José'],
            ],
            UbiscamasPayload::nombresListaFromPayload($data)
        );
    }

    public function test_nombres_lista_vacia_sin_asistentes(): void
    {
        $data = self::payload();
        $data['camas_con_asistentes'] = [];

        self::assertSame([], UbiscamasPayload::nombresListaFromPayload($data));
    }
}
