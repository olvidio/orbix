<?php

namespace src\asignaturas\domain\value_objects;

use src\shared\domain\helpers\FuncTablasSupport;

final class PlanEstudios
{
    public const PLAN_1997 = 1997;
    public const PLAN_2026 = 2026;

    /** @var list<int> */
    public const VALORES_POSIBLES = [self::PLAN_1997, self::PLAN_2026];

    /** @var list<int> */
    private array $values;

    /**
     * @param list<int>|array<int, int|string> $planes
     */
    public function __construct(array $planes)
    {
        $normalized = [];
        foreach ($planes as $plan) {
            if (is_string($plan)) {
                $plan = trim($plan);
                if ($plan === '') {
                    continue;
                }
                if (!ctype_digit($plan)) {
                    continue;
                }
                $plan = (int) $plan;
            }
            if (!is_int($plan)) {
                continue;
            }
            $this->validate($plan);
            $normalized[] = $plan;
        }
        $this->values = array_values(array_unique($normalized));
    }

    private function validate(int $year): void
    {
        if ($year < 1928 || $year > 2200 || $year < 1000 || $year > 9999) {
            throw new \InvalidArgumentException('Plan de estudios inválido');
        }
        if (!in_array($year, self::VALORES_POSIBLES, true)) {
            throw new \InvalidArgumentException('Plan de estudios no permitido');
        }
    }

    public function contains(int $year): bool
    {
        return in_array($year, $this->values, true);
    }

    /**
     * @return list<int>
     */
    public function toArray(): array
    {
        return $this->values;
    }

    public function __toString(): string
    {
        return '{' . implode(',', $this->values) . '}';
    }

    /**
     * @param list<int>|array<int, int|string> $planes
     */
    public static function fromArray(array $planes): self
    {
        return new self($planes);
    }

    /**
     * @param list<int>|array<int, int|string>|null $planes
     */
    public static function fromNullableArray(?array $planes): ?self
    {
        if ($planes === null || $planes === []) {
            return null;
        }

        return new self($planes);
    }

    public static function fromPgString(string $pgArray): ?self
    {
        $parsed = FuncTablasSupport::arrayPgInteger2php($pgArray);

        return self::fromNullableArray($parsed);
    }

    /**
     * @return array<int, string>
     */
    public static function getArrayOpciones(): array
    {
        return [
            self::PLAN_1997 => (string) self::PLAN_1997,
            self::PLAN_2026 => (string) self::PLAN_2026,
        ];
    }
}
