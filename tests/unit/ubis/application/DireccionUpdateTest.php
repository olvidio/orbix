<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\DireccionUpdate;

final class DireccionUpdateTest extends TestCase
{
    public function test_obj_dir_vacio_devuelve_mensaje_de_obj_desconocido(): void
    {
        $msg = DireccionUpdate::execute([]);
        $this->assertStringContainsString('obj_dir desconocido', $msg);
    }

    public function test_obj_dir_desconocido_devuelve_mensaje_de_obj_desconocido(): void
    {
        $msg = DireccionUpdate::execute(['obj_dir' => 'NoExiste', 'id_ubi' => 1]);
        $this->assertSame('obj_dir desconocido: NoExiste', $msg);
    }
}
