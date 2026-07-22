<?php

declare(strict_types=1);

namespace Tests\unit\notas;

use PHPUnit\Framework\TestCase;
use src\notas\application\support\ActaDlGuard;
use src\notas\domain\contracts\MapaPrefijoActaEsquemaRepositoryInterface;

final class ActaDlGuardTest extends TestCase
{
    private function mapa(array $prefToBase): MapaPrefijoActaEsquemaRepositoryInterface
    {
        return new class ($prefToBase) implements MapaPrefijoActaEsquemaRepositoryInterface {
            /** @param array<string, string> $map */
            public function __construct(private array $map)
            {
            }

            public function esquemaBasePorPrefijo(string $prefijo): ?string
            {
                return $this->map[strtolower(trim($prefijo))] ?? null;
            }

            public function esquemaDestinoDesdeActa(string $acta, string $suffix): ?string
            {
                return null;
            }

            public function prefijosPorEsquemaBase(string $esquemaBase): array
            {
                $out = [];
                foreach ($this->map as $pref => $base) {
                    if (strcasecmp($base, $esquemaBase) === 0) {
                        $out[] = $pref;
                    }
                }

                return $out;
            }

            public function prefijoPerteneceAEsquema(string $prefijo, string $esquemaBase): bool
            {
                $b = $this->esquemaBasePorPrefijo($prefijo);

                return $b !== null && strcasecmp($b, $esquemaBase) === 0;
            }

            public function fusionesEsquemaBase(): array
            {
                return [];
            }

            public function upsertPrefijo(string $prefijo, string $esquemaBase, ?string $notas = null): void
            {
            }

            public function reasignarEsquemaBase(string $esquemaBaseAnterior, string $esquemaBaseNuevo): void
            {
            }
        };
    }

    public function test_permite_mi_dele(): void
    {
        $guard = new ActaDlGuard($this->mapa([]));
        $this->assertSame('', $guard->ensureOwnership('dlal 1/24', 'dlal', 'nueva'));
    }

    public function test_bloquea_otra_dl_en_nueva(): void
    {
        $_SESSION['session_auth'] = ['esquema' => 'H-dlalv'];
        $guard = new ActaDlGuard($this->mapa(['dlz' => 'H-dlal']));
        $err = $guard->ensureOwnership('dlz 1/24', 'dlal', 'nueva');
        $this->assertNotSame('', $err);
    }

    public function test_permite_prefijo_fusionado_al_modificar(): void
    {
        $_SESSION['session_auth'] = ['esquema' => 'H-dlalv'];
        $guard = new ActaDlGuard($this->mapa(['dlz' => 'H-dlal', 'dlal' => 'H-dlal']));
        $this->assertSame('', $guard->ensureOwnership('dlz 1/24', 'dlal', 'modificar'));
        $this->assertSame('', $guard->ensureOwnership('dlz 1/24', 'dlal', 'eliminar'));
    }
}
