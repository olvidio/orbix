<?php

declare(strict_types=1);

namespace src\notas\infrastructure\persistence\postgresql;

use PDO;
use src\notas\domain\contracts\MapaPrefijoActaEsquemaRepositoryInterface;
use src\shared\infrastructure\GlobalPdo;

/**
 * Lectura/escritura de `public.mapa_prefijo_acta_esquema` (BD de delegación / región).
 */
final class PgMapaPrefijoActaEsquemaRepository implements MapaPrefijoActaEsquemaRepositoryInterface
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? GlobalPdo::get('oDB');
    }

    public function esquemaBasePorPrefijo(string $prefijo): ?string
    {
        $pref = strtolower(trim($prefijo));
        if ($pref === '' || str_starts_with($pref, 'fin')) {
            return null;
        }

        $stmt = $this->pdo->prepare(
            'SELECT esquema_base FROM public.mapa_prefijo_acta_esquema WHERE pref = :pref'
        );
        $stmt->execute(['pref' => $pref]);
        $base = $stmt->fetchColumn();
        if ($base === false || $base === null || $base === '') {
            return null;
        }

        return (string) $base;
    }

    public function esquemaDestinoDesdeActa(string $acta, string $suffix): ?string
    {
        if ($suffix !== 'v' && $suffix !== 'f') {
            throw new \InvalidArgumentException('suffix debe ser v o f');
        }
        $acta = trim($acta);
        if ($acta === '') {
            return null;
        }
        $pref = strtolower(trim(explode(' ', $acta, 2)[0]));
        $base = $this->esquemaBasePorPrefijo($pref);
        if ($base === null) {
            return null;
        }

        return $base . $suffix;
    }

    public function prefijosPorEsquemaBase(string $esquemaBase): array
    {
        $base = trim($esquemaBase);
        if ($base === '') {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT pref FROM public.mapa_prefijo_acta_esquema
             WHERE esquema_base = :base
             ORDER BY pref'
        );
        $stmt->execute(['base' => $base]);
        /** @var list<mixed> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $out = [];
        foreach ($rows as $pref) {
            if (is_string($pref) && $pref !== '') {
                $out[] = $pref;
            }
        }

        return $out;
    }

    public function prefijoPerteneceAEsquema(string $prefijo, string $esquemaBase): bool
    {
        $mapped = $this->esquemaBasePorPrefijo($prefijo);
        if ($mapped === null) {
            return false;
        }

        return strcasecmp($mapped, trim($esquemaBase)) === 0;
    }

    public function fusionesEsquemaBase(): array
    {
        $stmt = $this->pdo->query(
            "SELECT pref, esquema_base, notas
             FROM public.mapa_prefijo_acta_esquema
             WHERE notas ILIKE '%fusion%'"
        );
        if ($stmt === false) {
            return [];
        }

        $fusiones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $prefRaw = $row['pref'] ?? '';
            $destinoRaw = $row['esquema_base'] ?? '';
            $pref = strtolower(trim(is_scalar($prefRaw) ? (string) $prefRaw : ''));
            $destino = trim(is_scalar($destinoRaw) ? (string) $destinoRaw : '');
            if ($pref === '' || $destino === '' || !str_contains($destino, '-')) {
                continue;
            }
            [$region] = explode('-', $destino, 2);
            $origen = $region . '-' . $pref;
            if (strcasecmp($origen, $destino) === 0) {
                continue;
            }
            $fusiones[$origen] = $destino;
        }

        return $fusiones;
    }

    public function upsertPrefijo(string $prefijo, string $esquemaBase, ?string $notas = null): void
    {
        $pref = strtolower(trim($prefijo));
        $base = trim($esquemaBase);
        if ($pref === '' || $base === '') {
            return;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO public.mapa_prefijo_acta_esquema (pref, esquema_base, notas)
             VALUES (:pref, :base, :notas)
             ON CONFLICT (pref) DO UPDATE
             SET esquema_base = EXCLUDED.esquema_base,
                 notas = COALESCE(EXCLUDED.notas, public.mapa_prefijo_acta_esquema.notas)'
        );
        $stmt->execute([
            'pref' => $pref,
            'base' => $base,
            'notas' => $notas,
        ]);
    }

    public function reasignarEsquemaBase(string $esquemaBaseAnterior, string $esquemaBaseNuevo): void
    {
        $viejo = trim($esquemaBaseAnterior);
        $nuevo = trim($esquemaBaseNuevo);
        if ($viejo === '' || $nuevo === '' || strcasecmp($viejo, $nuevo) === 0) {
            return;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE public.mapa_prefijo_acta_esquema
             SET esquema_base = :nuevo
             WHERE esquema_base = :viejo'
        );
        $stmt->execute(['nuevo' => $nuevo, 'viejo' => $viejo]);
    }
}
