<?php

declare(strict_types=1);

namespace Tests\unit\permisos\domain;

use PHPUnit\Framework\TestCase;
use src\permisos\domain\MenuDlPermissionBits;

final class MenuDlPermissionBitsTest extends TestCase
{
    public function test_map_tiene_los_valores_historicos(): void
    {
        $map = MenuDlPermissionBits::map();

        $this->assertCount(26, $map);
        $this->assertSame(1, $map['adl']);
        $this->assertSame(1 << 1, $map['agd']);
        $this->assertSame(1 << 2, $map['aop']);
        $this->assertSame(1 << 16, $map['ctr']);
        $this->assertSame(1 << 20, $map['casa']);
        $this->assertSame(16776183, $map['admin_sf']);
        $this->assertSame(-1, $map['admin_sv']);
    }

    public function test_los_alias_comparten_bit(): void
    {
        $map = MenuDlPermissionBits::map();

        $this->assertSame($map['adl'], $map['pr']);
        $this->assertSame($map['scdl'], $map['scr']);
        $this->assertSame($map['vcsd'], $map['vcsr']);
    }

    public function test_los_bits_simples_no_se_solapan_salvo_alias(): void
    {
        $map = MenuDlPermissionBits::map();
        unset($map['admin_sf'], $map['admin_sv']);

        $valores = array_values($map);
        // 26 entradas - 2 admin = 24; 3 alias duplicados → 21 valores distintos.
        $this->assertCount(21, array_unique($valores));
        foreach (array_unique($valores) as $v) {
            $this->assertSame(1, substr_count(decbin($v), '1'), "$v debe ser una potencia de 2");
        }
    }

    public function test_admin_sv_tiene_todos_los_bits_y_admin_sf_no_tiene_todos(): void
    {
        $map = MenuDlPermissionBits::map();

        foreach ($map as $nom => $num) {
            $this->assertSame($num, $map['admin_sv'] & $num, "admin_sv incluye $nom");
        }
        foreach (['des', 'vcsd', 'vcsr'] as $faltante) {
            $this->assertSame(0, $map['admin_sf'] & $map[$faltante], "admin_sf no incluye $faltante");
        }
        foreach (['adl', 'agd', 'aop', 'sg', 'ctr', 'casa'] as $incluido) {
            $this->assertSame($map[$incluido], $map['admin_sf'] & $map[$incluido], "admin_sf incluye $incluido");
        }
    }

    public function test_lista_txt_exige_valor_exacto(): void
    {
        $this->assertSame('agd', MenuDlPermissionBits::listaTxt(2));
        $this->assertSame('', MenuDlPermissionBits::listaTxt(3), 'suma de dos bits: ninguna etiqueta exacta');
    }

    public function test_lista_txt_con_alias_devuelve_todas_las_etiquetas_del_valor(): void
    {
        $this->assertSame('adl, pr', MenuDlPermissionBits::listaTxt(1));
        $this->assertSame('scdl, scr', MenuDlPermissionBits::listaTxt(1 << 5));
        $this->assertSame('vcsd, vcsr', MenuDlPermissionBits::listaTxt(1 << 10));
    }

    public function test_lista_txt_cero_y_admin(): void
    {
        $this->assertSame('', MenuDlPermissionBits::listaTxt(0));
        $this->assertSame('admin_sv', MenuDlPermissionBits::listaTxt(-1));
        $this->assertSame('admin_sf', MenuDlPermissionBits::listaTxt(16776183));
    }

    public function test_lista_txt2_lista_cada_bit_contenido(): void
    {
        $this->assertSame('adl, pr, agd', MenuDlPermissionBits::listaTxt2(3));
        $this->assertSame('aop', MenuDlPermissionBits::listaTxt2(4));
        $this->assertSame('', MenuDlPermissionBits::listaTxt2(0));
    }

    public function test_lista_txt2_de_admin_sv_lista_todo(): void
    {
        $esperado = implode(', ', array_keys(MenuDlPermissionBits::map()));

        $this->assertSame($esperado, MenuDlPermissionBits::listaTxt2(-1));
    }

    public function test_lista_txt2_de_admin_sf_incluye_su_etiqueta_pero_no_admin_sv(): void
    {
        $txt = MenuDlPermissionBits::listaTxt2(16776183);
        $partes = explode(', ', $txt);

        $this->assertContains('admin_sf', $partes);
        $this->assertContains('agd', $partes);
        $this->assertContains('adl', $partes);
        $this->assertNotContains('admin_sv', $partes);
        $this->assertNotContains('des', $partes);
        $this->assertNotContains('vcsd', $partes);
    }
}
