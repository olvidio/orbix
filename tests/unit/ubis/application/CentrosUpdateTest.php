<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\CentrosUpdate;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

final class CentrosUpdateTest extends TestCase
{
    private CentrosUpdate $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new CentrosUpdate($this->createMock(CentroDlRepositoryInterface::class));
    }

    public function test_sin_id_ubi_retorna_vacio(): void
    {
        $this->assertSame('', $this->useCase->execute([]));
    }

    public function test_id_ubi_cero_retorna_vacio(): void
    {
        $this->assertSame('', $this->useCase->execute(['id_ubi' => 0]));
    }

    public function test_id_ubi_no_entero_se_castea_y_si_es_cero_retorna_vacio(): void
    {
        $this->assertSame('', $this->useCase->execute(['id_ubi' => 'abc']));
    }
}
