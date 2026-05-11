<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\notas\application\ExaminadoresSearchData;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\infrastructure\persistence\postgresql\PgActaTribunalDlRepository;

final class ExaminadoresSearchDataTest extends TestCase
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

    public function test_delega_en_repositorio(): void
    {
        $repo = $this->getMockBuilder(PgActaTribunalDlRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getJsonExaminadores'])
            ->getMock();
        $repo->expects($this->once())
            ->method('getJsonExaminadores')
            ->with('gar')
            ->willReturn('[]');

        $GLOBALS['container'] = $this->containerFromMap([
            ActaTribunalDlRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('[]', ExaminadoresSearchData::execute(['search' => 'gar']));
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
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
