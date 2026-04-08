<?php

namespace Tests\factories\shared;

use src\shared\domain\entity\ColaMail;
use src\shared\domain\value_objects\ColaMailId;

/**
 * Factory para crear instancias de ColaMail para tests
 */
class ColaMailFactory
{
    private static function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function createSimple(?string $uuid = null): ColaMail
    {
        $uuid = $uuid ?? self::generateUuid();

        $oColaMail = new ColaMail();
        $oColaMail->setUuid_item(ColaMailId::fromString($uuid));
        $oColaMail->setMail_to('test@test.com');
        $oColaMail->setSubject('test subject');
        $oColaMail->setMessage('test message');

        return $oColaMail;
    }
}
