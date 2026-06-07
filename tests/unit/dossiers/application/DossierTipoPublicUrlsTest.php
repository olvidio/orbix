<?php

declare(strict_types=1);

namespace Tests\unit\dossiers\application;

use PHPUnit\Framework\TestCase;
use src\dossiers\application\DossierTipoFileSuffixResolver;
use src\dossiers\application\DossierTipoPublicUrls;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;

final class DossierTipoPublicUrlsTest extends TestCase
{
    public function test_lanza_si_no_hay_tipo(): void
    {
        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('findById')->with(404)->willReturn(null);

        $useCase = new DossierTipoPublicUrls(
            $repo,
            DossierTipoFileSuffixResolver::fromDefaultProjectRoot(),
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('404');
        $useCase->relativeFormControllerInstance(404);
    }
}
