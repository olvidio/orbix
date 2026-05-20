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

    public function test_reparar_convierte_not_null_suelto_en_alter_tras_inherits(): void
    {
        $sql = <<<'SQL'
CREATE TABLE "H-dlb".cd_cargos_activ_dl (
    NOT NULL id_item
)
INHERITS (global.d_cargos_activ)
ALTER TABLE ONLY "H-dlb".cd_cargos_activ_dl OWNER TO "H-dlb";
SQL;

        $out = DBEsquemaCreate::repararVolcadoHeredadoYCompatibilidad($sql);

        $this->assertStringContainsString("CREATE TABLE \"H-dlb\".cd_cargos_activ_dl (\n)", $out);
        $this->assertStringContainsString('INHERITS (global.d_cargos_activ);', $out);
        $this->assertStringContainsString(
            'ALTER TABLE ONLY "H-dlb".cd_cargos_activ_dl ALTER COLUMN id_item SET NOT NULL;',
            $out,
        );
        $this->assertStringNotContainsString("NOT NULL id_item\n)", $out);
    }

    public function test_reparar_id_schema_sin_parentesis_antes_de_inherits(): void
    {
        $sql = <<<'SQL'
CREATE TABLE "H-dlf".cd_ejemplo (
    NOT NULL id_schema,
    NOT NULL id_item
INHERITS (global.d_ejemplo);
ALTER TABLE ONLY "H-dlf".cd_ejemplo OWNER TO "H-dlf";
SQL;

        $out = DBEsquemaCreate::repararVolcadoHeredadoYCompatibilidad($sql);

        $this->assertStringContainsString('INHERITS (global.d_ejemplo);', $out);
        $this->assertStringContainsString(
            'ALTER TABLE ONLY "H-dlf".cd_ejemplo ALTER COLUMN id_schema SET NOT NULL;',
            $out,
        );
        $this->assertStringNotContainsString("NOT NULL id_schema,\n", $out);
    }

    public function test_reparar_create_table_only_sin_parentesis_antes_de_inherits(): void
    {
        $sql = <<<'SQL'
CREATE TABLE ONLY "H-dlf"."cd_ejemplo" (
    NOT NULL id_schema,
INHERITS (global.d_ejemplo);
SQL;

        $out = DBEsquemaCreate::repararVolcadoHeredadoYCompatibilidad($sql);

        $this->assertStringContainsString('CREATE TABLE ONLY "H-dlf"."cd_ejemplo" (', $out);
        $this->assertStringContainsString(
            'ALTER TABLE ONLY "H-dlf"."cd_ejemplo" ALTER COLUMN id_schema SET NOT NULL;',
            $out,
        );
        $this->assertStringNotContainsString("NOT NULL id_schema,\n", $out);
    }
}
