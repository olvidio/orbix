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

    public function test_wildcard_sv_is_not_equal_to_zero_padded_int(): void
    {
        $wildcard = new ActividadTipoIdTxt('1.....');
        $numeric = new ActividadTipoIdTxt('000001');
        $this->assertFalse($wildcard->equals($numeric));
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

    public function test_too_short_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('exactamente 6 caracteres');
        new ActividadTipoIdTxt('12345');
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
