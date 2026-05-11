<?php

declare(strict_types=1);

namespace Tests\unit\menus\domain;

use PHPUnit\Framework\TestCase;
use src\menus\domain\PermisoMenuBits;

final class PermisoMenuBitsTest extends TestCase
{
    public function test_map_includes_jefe_zona_and_distinct_admin_masks_from_dl(): void
    {
        $map = PermisoMenuBits::map();
        $this->assertSame(1 << 17, $map['jefeZona']);
        $this->assertSame(1 << 21, $map['admin_sf']);
        $this->assertSame(1 << 25, $map['admin_sv']);
    }

    public function test_combine_selected_bits_or_merge(): void
    {
        $this->assertSame(3, PermisoMenuBits::combineSelectedBits([1, 2]));
        $this->assertSame(0, PermisoMenuBits::combineSelectedBits([]));
    }

    public function test_lista_txt2_includes_matching_flags(): void
    {
        $txt = PermisoMenuBits::listaTxt2(1);
        $this->assertStringContainsString('adl', $txt);
        $this->assertStringContainsString('pr', $txt);
    }
}
