<?php

declare(strict_types=1);

namespace Tests\unit\frontend\personas;

use frontend\personas\helpers\PersonasPayload;
use frontend\shared\security\HashFSignedLink;
use PHPUnit\Framework\TestCase;

final class PersonasPayloadTest extends TestCase
{
    private string|false $previousPublicBase;

    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('personas-payload-link-spec-test');
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

    public function test_select_row_preserves_backend_home_link_spec_for_frontend_signing(): void
    {
        $row = PersonasPayload::selectFilaRow([
            'id_nom' => 42,
            'id_tabla' => 'n',
            'nom' => 'Persona de prueba',
            'home_link_spec' => [
                'path' => 'frontend/personas/controller/home_persona.php',
                'query' => ['id_nom' => 42, 'id_tabla' => 'n', 'obj_pau' => 'PersonaN'],
            ],
        ]);

        $this->assertSame(
            [
                'path' => 'frontend/personas/controller/home_persona.php',
                'query' => ['id_nom' => 42, 'id_tabla' => 'n', 'obj_pau' => 'PersonaN'],
            ],
            $row['home_link_spec']
        );

        $url = HashFSignedLink::tryFromSpec($row['home_link_spec']);
        $this->assertStringContainsString('/app/frontend/personas/controller/home_persona.php?', $url);
        $this->assertStringContainsString('id_nom=42', $url);
        $this->assertStringContainsString('h=', $url);
    }

    public function test_select_row_discards_malformed_home_link_spec(): void
    {
        $row = PersonasPayload::selectFilaRow([
            'id_nom' => 42,
            'id_tabla' => 'n',
            'nom' => 'Persona de prueba',
            'home_link_spec' => ['path' => 'frontend/personas/controller/home_persona.php'],
        ]);

        $this->assertNull($row['home_link_spec']);
    }
}
