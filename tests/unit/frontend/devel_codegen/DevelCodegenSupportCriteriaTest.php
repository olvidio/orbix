<?php

declare(strict_types=1);

namespace Tests\unit\frontend\devel_codegen;

use frontend\devel_codegen\helpers\DevelCodegenSupport;
use PHPUnit\Framework\TestCase;

final class DevelCodegenSupportCriteriaTest extends TestCase
{
    public function test_case_name_pascal_case(): void
    {
        self::assertSame('IdActiv', DevelCodegenSupport::criteriaCaseName('id_activ'));
        self::assertSame('Plaza', DevelCodegenSupport::criteriaCaseName('plaza'));
        self::assertSame('FIni', DevelCodegenSupport::criteriaCaseName('f_ini'));
    }

    public function test_case_name_starting_with_digit(): void
    {
        self::assertSame('Campo1x', DevelCodegenSupport::criteriaCaseName('1x'));
    }

    public function test_enum_source_is_valid_php_with_expected_cases(): void
    {
        $src = DevelCodegenSupport::criteriaFieldEnumSource('asistentes', 'Asistente', ['id_activ', 'id_nom', 'propio']);

        self::assertStringContainsString('namespace src\asistentes\domain\criteria;', $src);
        self::assertStringContainsString('enum AsistenteField: string implements CriteriaField', $src);
        self::assertStringContainsString("case IdActiv = 'id_activ';", $src);
        self::assertStringContainsString("case Propio = 'propio';", $src);

        $tmp = tempnam(sys_get_temp_dir(), 'enum');
        file_put_contents($tmp, $src);
        try {
            $out = [];
            exec('php -l ' . escapeshellarg($tmp) . ' 2>&1', $out, $code);
            self::assertSame(0, $code, implode("\n", $out));
        } finally {
            unlink($tmp);
        }
    }

    public function test_enum_source_skips_duplicate_cases(): void
    {
        $src = DevelCodegenSupport::criteriaFieldEnumSource('x', 'Y', ['a_b', 'a_b']);

        self::assertSame(1, substr_count($src, 'case AB'));
    }
}
