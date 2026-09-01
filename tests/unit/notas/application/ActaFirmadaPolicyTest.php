<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\notas\application\support\ActaFirmadaPolicy;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\value_objects\NotaSituacion;

final class ActaFirmadaPolicyTest extends TestCase
{
    public function test_vacio_y_cursada_no_bloquean(): void
    {
        $policy = new ActaFirmadaPolicy($this->createMock(ActaRepositoryInterface::class));
        $this->assertSame('', $policy->mensajeSiFirmada(''));
        $this->assertSame('', $policy->mensajeSiFirmada((string) NotaSituacion::CURSADA));
    }

    public function test_acta_sin_pdf_no_bloquea(): void
    {
        $acta = new Acta();
        $acta->setActa('dlb 1/26');
        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->with('dlb 1/26')->willReturn($acta);

        $policy = new ActaFirmadaPolicy($repo);
        $this->assertSame('', $policy->mensajeSiFirmada('dlb 1/26'));
        $this->assertFalse($policy->estaFirmada($acta));
    }

    public function test_acta_con_pdf_bloquea(): void
    {
        $acta = new Acta();
        $acta->setActa('dlb 1/26');
        $acta->setPdf('%PDF-fake');
        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->with('dlb 1/26')->willReturn($acta);

        $policy = new ActaFirmadaPolicy($repo);
        $this->assertNotSame('', $policy->mensajeSiFirmada('dlb 1/26'));
        $this->assertTrue($policy->estaFirmada($acta));
    }
}
