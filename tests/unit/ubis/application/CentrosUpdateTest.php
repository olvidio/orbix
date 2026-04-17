<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\CentrosUpdate;

final class CentrosUpdateTest extends TestCase
{
    public function test_sin_id_ubi_retorna_vacio(): void
    {
        $this->assertSame('', CentrosUpdate::execute([]));
    }

    public function test_id_ubi_cero_retorna_vacio(): void
    {
        $this->assertSame('', CentrosUpdate::execute(['id_ubi' => 0]));
    }

    public function test_id_ubi_no_entero_se_castea_y_si_es_cero_retorna_vacio(): void
    {
        $this->assertSame('', CentrosUpdate::execute(['id_ubi' => 'abc']));
    }
}
