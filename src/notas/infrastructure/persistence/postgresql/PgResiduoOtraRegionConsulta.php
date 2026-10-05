<?php

declare(strict_types=1);

namespace src\notas\infrastructure\persistence\postgresql;

use PDO;
use src\notas\domain\contracts\ResiduoOtraRegionConsultaInterface;
use src\shared\infrastructure\GlobalPdo;

/**
 * Cruza el residuo de `e_notas_otra_region_stgr` con las actas de `e_notas_dl`
 * y con los certificados emitidos de esta base (sv o sf de la sesión).
 */
final class PgResiduoOtraRegionConsulta implements ResiduoOtraRegionConsultaInterface
{
    private PDO $pdo;

    /** @var list<string>|null */
    private ?array $esquemasLegibles = null;

    /** @var list<string>|null */
    private ?array $esquemasSinPermiso = null;

    public function __construct()
    {
        $this->pdo = GlobalPdo::get('oDBP');
    }

    public function esquemasSinPermiso(): array
    {
        $this->clasificarEsquemasResiduo();

        return $this->esquemasSinPermiso ?? [];
    }

    public function listar(): array
    {
        $this->clasificarEsquemasResiduo();
        $esquemas = $this->esquemasLegibles ?? [];
        if ($esquemas === []) {
            return [];
        }

        $partes = [];
        foreach ($esquemas as $esquema) {
            $quoted = '"' . str_replace('"', '""', $esquema) . '"';
            $partes[] = "SELECT " . $this->pdo->quote($esquema) . " AS esquema,
                n.id_nom, n.id_asignatura, n.id_situacion,
                COALESCE(n.tipo_acta, 1) AS tipo_acta,
                COALESCE(n.acta, '') AS acta,
                COALESCE(n.f_acta::text, '') AS f_acta,
                COALESCE(n.detalle, '') AS detalle,
                n.json_certificados::text AS json_certificados
                FROM {$quoted}.e_notas_otra_region_stgr n";
        }

        $resto = $this->esquemaDePaso();
        $nombrePaso = $resto === null
            ? "''"
            : "COALESCE((
                SELECT trim(both ' ' FROM coalesce(p.apellido1, '') || ', ' || coalesce(p.nom, ''))
                FROM " . '"' . str_replace('"', '""', $resto) . '"' . ".p_de_paso_ex p
                WHERE p.id_nom = r.id_nom
                LIMIT 1
            ), '')";

        $sql = "SELECT r.*,
                COALESCE(NULLIF({$nombrePaso}, ''), (
                    SELECT trim(both ' ' FROM coalesce(g.apellido1, '') || ', ' || coalesce(g.nom, ''))
                    FROM global.personas g
                    WHERE g.id_nom = r.id_nom
                    ORDER BY CASE WHEN g.situacion = 'A' THEN 0 ELSE 1 END, g.f_situacion DESC NULLS LAST
                    LIMIT 1
                ), '') AS nombre,
                {$this->sqlHayActaDl()}
            FROM (" . implode(' UNION ALL ', $partes) . ") r
            ORDER BY r.id_nom, r.id_asignatura";

        $stmt = $this->pdo->query($sql);
        if ($stmt === false) {
            throw new \RuntimeException(_('No se pudo leer el residuo de notas de otras regiones.'));
        }

        $enModulo = $this->certificadosEnModulo();
        $filas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $certificados = $this->certificadosDeJson($row['json_certificados'] ?? null);
            $enEsteModulo = false;
            foreach ($certificados as $numero) {
                if (isset($enModulo[$numero])) {
                    $enEsteModulo = true;
                    break;
                }
            }
            $filas[] = [
                'esquema' => (string) ($row['esquema'] ?? ''),
                'id_nom' => (int) ($row['id_nom'] ?? 0),
                'id_asignatura' => (int) ($row['id_asignatura'] ?? 0),
                'id_situacion' => (int) ($row['id_situacion'] ?? 0),
                'tipo_acta' => (int) ($row['tipo_acta'] ?? 1),
                'acta' => (string) ($row['acta'] ?? ''),
                'f_acta' => substr((string) ($row['f_acta'] ?? ''), 0, 10),
                'detalle' => (string) ($row['detalle'] ?? ''),
                'nombre' => (string) ($row['nombre'] ?? ''),
                'hay_acta_dl' => $this->esCierto($row['hay_acta_dl'] ?? false),
                'certificados' => $certificados,
                'certificado_en_modulo' => $enEsteModulo,
            ];
        }

        return $filas;
    }

    private function clasificarEsquemasResiduo(): void
    {
        if ($this->esquemasLegibles !== null && $this->esquemasSinPermiso !== null) {
            return;
        }

        $stmt = $this->pdo->query(
            "SELECT n.nspname AS esquema,
                    (has_schema_privilege(n.oid, 'USAGE') AND has_table_privilege(c.oid, 'SELECT')) AS puede
             FROM pg_class c
             JOIN pg_namespace n ON n.oid = c.relnamespace
             WHERE c.relname = 'e_notas_otra_region_stgr'
               AND c.relkind = 'r'
               AND n.nspname NOT LIKE 'pg_%'
               AND n.nspname <> 'information_schema'
             ORDER BY n.nspname"
        );
        if ($stmt === false) {
            throw new \RuntimeException(_('No se pudo leer el residuo de notas de otras regiones.'));
        }

        $legibles = [];
        $sinPermiso = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $nombre = $row['esquema'] ?? null;
            if (!is_string($nombre) || preg_match('/^[A-Za-z0-9_-]+$/', $nombre) !== 1) {
                continue;
            }
            if ($this->esCierto($row['puede'] ?? false)) {
                $legibles[] = $nombre;
            } else {
                $sinPermiso[] = $nombre;
            }
        }

        $this->esquemasLegibles = $legibles;
        $this->esquemasSinPermiso = $sinPermiso;
    }

    private function sqlHayActaDl(): string
    {
        $esquemas = $this->esquemasConTablaLegible('e_notas_dl');
        if ($esquemas === []) {
            return 'false AS hay_acta_dl';
        }

        $partes = [];
        foreach ($esquemas as $esquema) {
            $partes[] = 'SELECT id_nom, id_asignatura FROM ONLY '
                . $this->quoteIdent($esquema) . '.e_notas_dl';
        }

        return 'EXISTS (
            SELECT 1 FROM (' . implode(' UNION ALL ', $partes) . ') a
            WHERE a.id_nom = r.id_nom AND a.id_asignatura = r.id_asignatura
        ) AS hay_acta_dl';
    }

    private function esquemaDePaso(): ?string
    {
        $esquemas = $this->esquemasConTablaLegible('p_de_paso_ex');
        foreach ($esquemas as $nombre) {
            if (str_starts_with($nombre, 'resto')) {
                return $nombre;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function esquemasConTablaLegible(string $relname): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT n.nspname
             FROM pg_class c
             JOIN pg_namespace n ON n.oid = c.relnamespace
             WHERE c.relname = :relname
               AND c.relkind = 'r'
               AND n.nspname NOT LIKE 'pg_%'
               AND n.nspname <> 'information_schema'
               AND has_schema_privilege(n.oid, 'USAGE')
               AND has_table_privilege(c.oid, 'SELECT')
             ORDER BY n.nspname"
        );
        if ($stmt === false || $stmt->execute(['relname' => $relname]) === false) {
            return [];
        }

        $esquemas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $nombre) {
            if (is_string($nombre) && preg_match('/^[A-Za-z0-9_-]+$/', $nombre) === 1) {
                $esquemas[] = $nombre;
            }
        }

        return $esquemas;
    }

    private function quoteIdent(string $nombre): string
    {
        return '"' . str_replace('"', '""', $nombre) . '"';
    }

    /**
     * @return array<string, true>
     */
    private function certificadosEnModulo(): array
    {
        $partes = [];
        foreach (['e_certificados_emitidos', 'e_certificados_rstgr'] as $relname) {
            foreach ($this->esquemasConTablaLegible($relname) as $esquema) {
                $partes[] = 'SELECT certificado FROM ONLY ' . $this->quoteIdent($esquema) . '.' . $relname
                    . " WHERE certificado IS NOT NULL AND certificado <> ''";
            }
        }
        if ($partes === []) {
            return [];
        }
        $stmt = $this->pdo->query('SELECT DISTINCT certificado FROM (' . implode(' UNION ALL ', $partes) . ') c');
        if ($stmt === false) {
            return [];
        }
        $presentes = [];
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $numero) {
            if (is_string($numero) && $numero !== '') {
                $presentes[$numero] = true;
            }
        }

        return $presentes;
    }

    /**
     * @return list<string>
     */
    private function certificadosDeJson(mixed $raw): array
    {
        if (is_array($raw)) {
            $decoded = $raw;
        } elseif (is_string($raw) && $raw !== '' && $raw !== 'null') {
            $decoded = json_decode($raw, true);
        } else {
            return [];
        }
        if (!is_array($decoded)) {
            return [];
        }

        $numeros = [];
        foreach ($decoded as $item) {
            $numero = null;
            if (is_array($item)) {
                $numero = $item['certificado'] ?? null;
            } elseif (is_object($item)) {
                $numero = $item->certificado ?? null;
            }
            if (is_string($numero) && $numero !== '') {
                $numeros[] = $numero;
            }
        }

        return $numeros;
    }

    private function esCierto(mixed $valor): bool
    {
        return $valor === true || $valor === 1 || $valor === '1' || $valor === 't' || $valor === 'true';
    }
}
