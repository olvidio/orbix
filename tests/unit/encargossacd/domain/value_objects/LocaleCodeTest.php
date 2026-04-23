<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\LocaleCode;
use Tests\myTest;

class LocaleCodeTest extends myTest
{
    public function test_create_valid_localeCode()
    {
        $localeCode = new LocaleCode('test value');
        $this->assertEquals('test value', $localeCode->value());
    }

    public function test_equals_returns_true_for_same_localeCode()
    {
        $localeCode1 = new LocaleCode('test value');
        $localeCode2 = new LocaleCode('test value');
        $this->assertTrue($localeCode1->equals($localeCode2));
    }

    public function test_equals_returns_false_for_different_localeCode()
    {
        $localeCode1 = new LocaleCode('test value');
        $localeCode2 = new LocaleCode('alternative value');
        $this->assertFalse($localeCode1->equals($localeCode2));
    }

    public function test_to_string_returns_localeCode_value()
    {
        $localeCode = new LocaleCode('test value');
        $this->assertEquals('test value', (string)$localeCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $localeCode = LocaleCode::fromNullableString('test value');
        $this->assertInstanceOf(LocaleCode::class, $localeCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $localeCode = LocaleCode::fromNullableString(null);
        $this->assertNull($localeCode);
    }
}
