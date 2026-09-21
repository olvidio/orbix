<?php

declare(strict_types=1);

namespace Tests\unit\frontend\layouts;

use frontend\shared\layouts\MenuNavigationLink;
use frontend\shared\security\HashF;
use PHPUnit\Framework\TestCase;

final class MenuNavigationLinkTest extends TestCase
{
    private string|false $previousPublicBase;

    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('menu-navigation-link-test');
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

    public function test_from_spec_signs_unsigned_menu_navigation_in_frontend(): void
    {
        $link = MenuNavigationLink::fromSpec([
            'path' => 'frontend/menus/controller/menus_que.php',
            'parametros' => 'id_grupmenu=7',
        ]);

        $this->assertSame(
            'https://orbix.test/app/frontend/menus/controller/menus_que.php',
            $link['full_url']
        );
        $this->assertSame(
            HashF::add_hash('id_grupmenu=7', $link['full_url']),
            $link['parametros']
        );
        parse_str($link['parametros'], $parametros);
        $this->assertSame('7', $parametros['id_grupmenu'] ?? null);
        $this->assertSame('1', $parametros['hpos'] ?? null);
        $this->assertArrayHasKey('h', $parametros);
    }

    public function test_from_spec_rejects_malformed_unsigned_data(): void
    {
        $this->assertSame(
            ['full_url' => '', 'parametros' => ''],
            MenuNavigationLink::fromSpec([
                'path' => 'frontend/menus/controller/menus_que.php',
                'parametros' => ['id_grupmenu' => 7],
            ])
        );
        $this->assertSame(
            ['full_url' => '', 'parametros' => ''],
            MenuNavigationLink::fromSpec(['path' => '', 'parametros' => 'id_grupmenu=7'])
        );
    }
}
