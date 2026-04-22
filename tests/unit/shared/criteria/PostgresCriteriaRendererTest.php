<?php

declare(strict_types=1);

namespace Tests\unit\shared\criteria;

use PHPUnit\Framework\TestCase;
use src\shared\domain\criteria\AndCriteria;
use src\shared\domain\criteria\FieldFilter;
use src\shared\domain\criteria\LegacyArrayCriteriaFactory;
use src\shared\domain\criteria\OrCriteria;
use src\shared\infrastructure\criteria\PostgresCriteriaRenderer;

final class PostgresCriteriaRendererTest extends TestCase
{
    public function testExampleFromUser(): void
    {
        $c = new AndCriteria([
            new FieldFilter('id_lugar', '=', 300456),
            new FieldFilter('f_ini', '>', '2026-02-01'),
        ]);
        $r = new PostgresCriteriaRenderer();
        [$w, $p] = $r->render($c);
        $this->assertStringContainsString('id_lugar = :', $w);
        $this->assertStringContainsString('f_ini > :', $w);
        $this->assertStringContainsString('AND', $w);
        $this->assertContains(300456, $p);
        $this->assertContains('2026-02-01', $p);
    }

    public function testLegacyFactoryMatchesSameShape(): void
    {
        $c = LegacyArrayCriteriaFactory::fromWhereAndOperatorArrays(
            ['id_lugar' => 300456, 'f_ini' => '2026-02-01'],
            ['f_ini' => '>'],
        );
        $r = new PostgresCriteriaRenderer();
        [$w, $p] = $r->render($c);
        $this->assertStringContainsString('f_ini', $w);
        $this->assertCount(2, $p);
    }

    public function testOrAndNested(): void
    {
        $c = new AndCriteria([
            new FieldFilter('status', '<', 4),
            new OrCriteria([
                new FieldFilter('id_tipo_activ', '~', '^1'),
                new FieldFilter('id_ubi', 'IS NULL', null),
            ]),
        ]);
        $r = new PostgresCriteriaRenderer();
        [$w,] = $r->render($c);
        $this->assertStringContainsString('status <', $w);
        $this->assertStringContainsString('OR', $w);
        $this->assertStringContainsString('IS NULL', $w);
    }
}
