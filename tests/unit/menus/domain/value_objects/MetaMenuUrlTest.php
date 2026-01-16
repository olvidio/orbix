<?php

namespace Tests\unit\menus\domain\value_objects;

use src\menus\domain\value_objects\MetaMenuUrl;
use Tests\myTest;

class MetaMenuUrlTest extends myTest
{
    public function test_create_valid_metaMenuUrl()
    {
        $metaMenuUrl = new MetaMenuUrl('test value');
        $this->assertEquals('test value', $metaMenuUrl->value());
    }

    public function test_equals_returns_true_for_same_metaMenuUrl()
    {
        $metaMenuUrl1 = new MetaMenuUrl('test value');
        $metaMenuUrl2 = new MetaMenuUrl('test value');
        $this->assertTrue($metaMenuUrl1->equals($metaMenuUrl2));
    }

    public function test_equals_returns_false_for_different_metaMenuUrl()
    {
        $metaMenuUrl1 = new MetaMenuUrl('test value');
        $metaMenuUrl2 = new MetaMenuUrl('alternative value');
        $this->assertFalse($metaMenuUrl1->equals($metaMenuUrl2));
    }

    public function test_to_string_returns_metaMenuUrl_value()
    {
        $metaMenuUrl = new MetaMenuUrl('test value');
        $this->assertEquals('test value', (string)$metaMenuUrl);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $metaMenuUrl = MetaMenuUrl::fromNullableString('test value');
        $this->assertInstanceOf(MetaMenuUrl::class, $metaMenuUrl);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $metaMenuUrl = MetaMenuUrl::fromNullableString(null);
        $this->assertNull($metaMenuUrl);
    }

}
