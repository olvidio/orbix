<?php

namespace Tests\unit\shared\domain\entity;

use src\shared\domain\entity\ColaMail;
use src\shared\domain\value_objects\ColaMailId;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class ColaMailTest extends myTest
{
    private ColaMail $ColaMail;

    public function setUp(): void
    {
        parent::setUp();
        $this->ColaMail = new ColaMail();
        $this->ColaMail->setUuid_item(new ColaMailId('550e8400-e29b-41d4-a716-446655440000'));
    }

    public function test_set_and_get_uuid_item()
    {
        $uuid_itemVo = new ColaMailId('550e8400-e29b-41d4-a716-446655440000');
        $this->ColaMail->setUuid_item($uuid_itemVo);
        $this->assertInstanceOf(ColaMailId::class, $this->ColaMail->getUuid_item());
        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $this->ColaMail->getUuid_item()->value());
    }

    public function test_set_and_get_mail_to()
    {
        $this->ColaMail->setMail_to('test');
        $this->assertEquals('test', $this->ColaMail->getMail_to());
    }

    public function test_set_and_get_message()
    {
        $this->ColaMail->setMessage('test');
        $this->assertEquals('test', $this->ColaMail->getMessage());
    }

    public function test_set_and_get_subject()
    {
        $this->ColaMail->setSubject('test');
        $this->assertEquals('test', $this->ColaMail->getSubject());
    }

    public function test_set_and_get_headers()
    {
        $this->ColaMail->setHeaders('test');
        $this->assertEquals('test', $this->ColaMail->getHeaders());
    }

    public function test_set_and_get_writed_by()
    {
        $this->ColaMail->setWrited_by('test');
        $this->assertEquals('test', $this->ColaMail->getWrited_by());
    }

    public function test_set_and_get_sended()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ColaMail->setSended($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ColaMail->getSended());
        $this->assertEquals('2024-01-15 10:30:00', $this->ColaMail->getSended()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes()
    {
        $colaMail = new ColaMail();
        $attributes = [
            'uuid_item' => new ColaMailId('550e8400-e29b-41d4-a716-446655440000'),
            'mail_to' => 'test',
            'message' => 'test',
            'subject' => 'test',
            'headers' => 'test',
            'writed_by' => 'test',
            'sended' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $colaMail->setAllAttributes($attributes);

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $colaMail->getUuid_item()->value());
        $this->assertEquals('test', $colaMail->getMail_to());
        $this->assertEquals('test', $colaMail->getMessage());
        $this->assertEquals('test', $colaMail->getSubject());
        $this->assertEquals('test', $colaMail->getHeaders());
        $this->assertEquals('test', $colaMail->getWrited_by());
        $this->assertEquals('2024-01-15 10:30:00', $colaMail->getSended()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes_with_string_values()
    {
        $colaMail = new ColaMail();
        $attributes = [
            'uuid_item' => '550e8400-e29b-41d4-a716-446655440000',
            'mail_to' => 'test',
            'message' => 'test',
            'subject' => 'test',
            'headers' => 'test',
            'writed_by' => 'test',
            'sended' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $colaMail->setAllAttributes($attributes);

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $colaMail->getUuid_item()->value());
        $this->assertEquals('test', $colaMail->getMail_to());
        $this->assertEquals('test', $colaMail->getMessage());
        $this->assertEquals('test', $colaMail->getSubject());
        $this->assertEquals('test', $colaMail->getHeaders());
        $this->assertEquals('test', $colaMail->getWrited_by());
        $this->assertEquals('2024-01-15 10:30:00', $colaMail->getSended()->format('Y-m-d H:i:s'));
    }
}
