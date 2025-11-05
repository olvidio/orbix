<?php

namespace Tests\unit\usuarios\domain\value_objects;

use src\usuarios\domain\value_objects\IdLocale;
use Tests\myTest;

class IdLocaleTest extends myTest
{
    public function test_create_valid_id_locale()
    {
        $idLocale = new IdLocale('en_US');
        $this->assertEquals('en_US', $idLocale->value());
    }

    public function test_empty_id_locale_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Locale identifier cannot be empty');
        new IdLocale('');
    }

    public function test_equals_returns_true_for_same_id_locale()
    {
        $idLocale1 = new IdLocale('en_US');
        $idLocale2 = new IdLocale('en_US');
        $this->assertTrue($idLocale1->equals($idLocale2));
    }

    public function test_equals_returns_false_for_different_id_locale()
    {
        $idLocale1 = new IdLocale('en_US');
        $idLocale2 = new IdLocale('es_ES');
        $this->assertFalse($idLocale1->equals($idLocale2));
    }

    public function test_to_string_returns_id_locale_value()
    {
        $idLocale = new IdLocale('en_US');
        $this->assertEquals('en_US', (string)$idLocale);
    }
}