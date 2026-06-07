<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\notas\application\ExaminadoresSearchData;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\infrastructure\persistence\postgresql\PgActaTribunalDlRepository;

final class ExaminadoresSearchDataTest extends TestCase
{
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

        $useCase = new ExaminadoresSearchData($repo);
        $this->assertSame('[]', $useCase->execute(['search' => 'gar']));
    }
}
