<?php

declare(strict_types=1);

namespace Tests\unit\frontend\encargossacd;

use frontend\encargossacd\helpers\PropuestasAjaxPayload;
use PHPUnit\Framework\TestCase;

final class PropuestasAjaxPayloadTest extends TestCase
{
    private string|false $previousPublicBase;

    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('propuestas-ajax-payload-test');
        $this->previousPublicBase = getenv('ORBIX_PUBLIC_APP_BASE_URL');
        putenv('ORBIX_PUBLIC_APP_BASE_URL=https://orbix.test/app');
    }

    protected function tearDown(): void
    {
        if ($this->previousPublicBase === false) {
            putenv('ORBIX_PUBLIC_APP_BASE_URL');
        } else {
            putenv('ORBIX_PUBLIC_APP_BASE_URL=' . $this->previousPublicBase);
        }
        parent::tearDown();
    }

    public function test_dedicacion_popup_renders_signed_form_in_frontend(): void
    {
        $out = PropuestasAjaxPayload::render([
            'success' => true,
            'popup' => 'dedicacion',
            'apellidos_nombre' => 'SACD',
            'desc_enc' => 'Encargo',
            'id_sacd' => 3,
            'id_item' => 4,
            'id_enc' => 5,
            'dedic_m' => '1',
            'dedic_t' => '2',
            'dedic_v' => '3',
        ]);

        $this->assertStringContainsString('<form method=', (string) ($out['html'] ?? ''));
        $this->assertStringContainsString('name="h"', (string) ($out['html'] ?? ''));
        $this->assertStringContainsString("name='dedic_m'", (string) ($out['html'] ?? ''));
    }

    public function test_row_data_renders_legacy_cells_only_in_frontend(): void
    {
        $out = PropuestasAjaxPayload::render([
            'success' => true,
            'row' => [
                'tipo' => 'celdas',
                'encargo_tipo' => 'titular',
                'id_item' => 4,
                'id_enc' => 5,
                'id_sacd' => 3,
                'nombre' => 'SACD',
                'nom_tipo' => 'titular',
            ],
        ]);

        $this->assertStringContainsString('id="titular_4"', (string) ($out['html'] ?? ''));
        $this->assertStringContainsString('fnjs_dedicacion', (string) ($out['html'] ?? ''));
    }
}
