<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure\persistence;

use PHPUnit\Framework\TestCase;
use src\shared\infrastructure\persistence\postgresql\DBEsquemaCreate;

final class DBEsquemaCreateVolcadoTest extends TestCase
{
    public function test_normalizar_quita_restrict_y_transaction_timeout(): void
    {
        $sql = <<<'SQL'
\restrict abc123
SET transaction_timeout = 0;
CREATE TABLE "H-dlb"."t" (
    id_item integer NOT NULL
);
\unrestrict abc123
SQL;

        $out = DBEsquemaCreate::normalizarVolcadoPgDumpParaPsql($sql);

        $this->assertStringNotContainsString('\restrict', $out);
        $this->assertStringNotContainsString('\unrestrict', $out);
        $this->assertStringNotContainsString('transaction_timeout', $out);
        $this->assertStringContainsString('integer NOT NULL', $out);
    }
}
