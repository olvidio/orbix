<?php

declare(strict_types=1);

namespace Tests\unit\menus\application;

use PHPUnit\Framework\TestCase;
use src\menus\application\ListaTemplatesMenus;
use src\menus\domain\contracts\TemplateMenuRepositoryInterface;

final class ListaTemplatesMenusTest extends TestCase
{
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_devuelve_opciones(): void
    {
        $repo = $this->createMock(TemplateMenuRepositoryInterface::class);
        $repo->method('getArrayTemplates')->willReturn(['t1' => 'T1']);

        $GLOBALS['container'] = $this->containerFromMap([
            TemplateMenuRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(['a_opciones' => ['t1' => 'T1']], (new ListaTemplatesMenus())());
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
