<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\PersonaPublicar;
use src\personas\domain\PersonaPublicacion;
use src\personas\domain\contracts\PersonaAllRepositoryInterface;

final class PersonaPublicarTest extends TestCase
{
    public function test_exige_dl(): void
    {
        $repo = $this->createMock(PersonaAllRepositoryInterface::class);
        $repo->expects($this->never())->method('marcarPublicadoPara');

        $uc = new PersonaPublicar($repo);
        $this->assertNotSame('', $uc->execute(1, 2, []));
    }

    public function test_publica_con_ttl_para_dl_concreta(): void
    {
        $repo = $this->createMock(PersonaAllRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('marcarPublicadoPara')
            ->with(
                10,
                3,
                'dlb',
                $this->callback(static fn($hasta): bool => $hasta instanceof \DateTimeInterface),
            )
            ->willReturn(true);

        $uc = new PersonaPublicar($repo);
        $this->assertSame('', $uc->execute(10, 3, 'dlbv'));
    }

    public function test_publica_todas_sin_ttl(): void
    {
        $repo = $this->createMock(PersonaAllRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('marcarPublicadoPara')
            ->with(10, 3, PersonaPublicacion::DL_TODAS, null)
            ->willReturn(true);

        $uc = new PersonaPublicar($repo);
        $this->assertSame('', $uc->execute(10, 3, PersonaPublicacion::DL_TODAS));
    }
}
