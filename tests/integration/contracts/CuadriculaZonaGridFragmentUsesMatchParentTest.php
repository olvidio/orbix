<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Los `use` de PHP son por fichero: un require no hereda los del padre.
 * Este test evita regresiones como "Class ConfigGlobal not found" en el fragmento.
 */
final class CuadriculaZonaGridFragmentUsesMatchParentTest extends TestCase
{
    private static function repoRoot(): string
    {
        return dirname(__DIR__, 3);
    }

    /**
     * @return list<string>
     */
    private function extractUseStatements(string $path): array
    {
        $content = file_get_contents($path) ?: '';
        $uses = [];
        if (preg_match_all('/^\s*(use\s+[^;]+;\s*)$/m', $content, $m)) {
            foreach ($m[1] as $line) {
                $uses[] = trim($line);
            }
        }

        return $uses;
    }

    public function test_fragment_includes_all_parent_builder_use_statements(): void
    {
        $parent = self::repoRoot() . '/src/misas/application/cuadricula_zona_grid_data_build.php';
        $fragment = self::repoRoot() . '/src/misas/application/_cuadricula_zona_grid_fragment.php';

        $this->assertFileExists($parent);
        $this->assertFileExists($fragment);

        $parentUses = $this->extractUseStatements($parent);
        $fragmentUses = $this->extractUseStatements($fragment);

        $this->assertNotEmpty($parentUses, 'El builder debería declarar use en cuadricula_zona_grid_data_build.php');
        $this->assertNotEmpty($fragmentUses, 'El fragmento debería declarar los mismos use que el builder');

        $fragmentSet = array_flip($fragmentUses);
        $missing = [];
        foreach ($parentUses as $useLine) {
            if (!isset($fragmentSet[$useLine])) {
                $missing[] = $useLine;
            }
        }

        $this->assertSame(
            [],
            $missing,
            "Faltan en _cuadricula_zona_grid_fragment.php los mismos `use` que en cuadricula_zona_grid_data_build.php:\n"
            . implode("\n", $missing)
        );
    }
}
