<?php

namespace Tests\unit\actividades\domain\value_objects;

use InvalidArgumentException;
use src\actividades\domain\value_objects\ActividadTipoIdTxt;
use Tests\myTest;

class ActividadTipoIdTxtTest extends myTest
{
    public function test_create_from_six_digit_string(): void
    {
        $vo = new ActividadTipoIdTxt('123456');
        $this->assertSame('123456', $vo->value());
    }

    public function test_create_trims_whitespace(): void
    {
        $vo = new ActividadTipoIdTxt(" 234567 ");
        $this->assertSame('234567', $vo->value());
    }

    public function test_from_string_factory(): void
    {
        $vo = ActividadTipoIdTxt::fromString('345678');
        $this->assertSame('345678', $vo->value());
    }

    public function test_preserves_wildcard_pattern_sv(): void
    {
        $vo = new ActividadTipoIdTxt('1.....');
        $this->assertSame('1.....', $vo->value());
    }

    public function test_preserves_wildcard_pattern_sf(): void
    {
        $vo = new ActividadTipoIdTxt('2.....');
        $this->assertSame('2.....', $vo->value());
    }

    public function test_preserves_partial_wildcard(): void
    {
        $vo = ActividadTipoIdTxt::fromString('12....');
        $this->assertSame('12....', $vo->value());
    }

    public function test_all_wildcard_pattern(): void
    {
        $vo = new ActividadTipoIdTxt('......');
        $this->assertSame('......', $vo->value());
    }

    public function test_zero_padded_int_equals_wildcard_after_canonicalize(): void
    {
        $wildcard = new ActividadTipoIdTxt('1.....');
        $numeric = new ActividadTipoIdTxt('000001');
        $this->assertTrue($wildcard->equals($numeric));
    }

    public function test_equals_true_for_same_value(): void
    {
        $a = new ActividadTipoIdTxt('111222');
        $b = new ActividadTipoIdTxt('111222');
        $this->assertTrue($a->equals($b));
    }

    public function test_equals_false_for_different_value(): void
    {
        $a = new ActividadTipoIdTxt('111222');
        $b = new ActividadTipoIdTxt('222111');
        $this->assertFalse($a->equals($b));
    }

    public function test_pads_short_numeric_prefix_with_dots(): void
    {
        $vo = new ActividadTipoIdTxt('17');
        $this->assertSame('17....', $vo->value());
    }

    public function test_canonicalize_unpadded_sr(): void
    {
        $this->assertSame('17....', ActividadTipoIdTxt::canonicalize('17'));
        $this->assertSame('17....', ActividadTipoIdTxt::canonicalize('000017'));
        $this->assertSame('17....', ActividadTipoIdTxt::canonicalize('17....'));
        $this->assertSame('1.....', ActividadTipoIdTxt::canonicalize('1'));
        $this->assertSame('1.....', ActividadTipoIdTxt::canonicalize('000001'));
        $this->assertSame('171001', ActividadTipoIdTxt::canonicalize('171001'));
        $this->assertSame('12....', ActividadTipoIdTxt::canonicalize('12....'));
    }

    public function test_lookup_keys_include_truncated_aliases(): void
    {
        $this->assertSame(['17....', '17', '000017'], ActividadTipoIdTxt::lookupKeys('17'));
        $this->assertSame(['17....', '17', '000017'], ActividadTipoIdTxt::lookupKeys('17....'));
        $this->assertSame(['171001'], ActividadTipoIdTxt::lookupKeys('171001'));
        $this->assertSame([], ActividadTipoIdTxt::lookupKeys(''));
    }

    public function test_from_form_parts_pads_sv_sr(): void
    {
        $this->assertSame('17....', ActividadTipoIdTxt::fromFormParts('1', '7', '', ''));
        $this->assertSame('171001', ActividadTipoIdTxt::fromFormParts('1', '7', '1', '001'));
        $this->assertSame('......', ActividadTipoIdTxt::fromFormParts('', '', '', ''));
    }

    public function test_apply_to_repository_where_uses_in_for_aliases(): void
    {
        [$where, $op] = ActividadTipoIdTxt::applyToRepositoryWhere([], [], '17');
        $this->assertSame(['17....', '17', '000017'], $where['id_tipo_activ_txt']);
        $this->assertSame('IN', $op['id_tipo_activ_txt']);

        [$where2, $op2] = ActividadTipoIdTxt::applyToRepositoryWhere([], [], '171001');
        $this->assertSame('171001', $where2['id_tipo_activ_txt']);
        $this->assertArrayNotHasKey('id_tipo_activ_txt', $op2);
    }

    public function test_zero_padded_sr_equals_wildcard(): void
    {
        $vo = new ActividadTipoIdTxt('000017');
        $this->assertSame('17....', $vo->value());
        $this->assertTrue($vo->equals(new ActividadTipoIdTxt('17....')));
    }

    public function test_empty_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('exactamente 6 caracteres');
        new ActividadTipoIdTxt('');
    }

    public function test_too_long_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ActividadTipoIdTxt('1234567');
    }

    public function test_non_numeric_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ActividadTipoIdTxt('12ab56');
    }
}
