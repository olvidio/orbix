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

    public function __construct()
    {
        $this->pdo = GlobalPdo::get('oDBP');
    }

    public function listar(): array
    {
        $esquemas = $this->esquemasConResiduo();
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
                EXISTS (
                    SELECT 1
                    FROM e_notas a
                    JOIN pg_class ca ON ca.oid = a.tableoid
                    WHERE ca.relname = 'e_notas_dl'
                      AND a.id_nom = r.id_nom
                      AND a.id_asignatura = r.id_asignatura
                ) AS hay_acta_dl
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

    /**
     * @return list<string>
     */
    private function esquemasConResiduo(): array
    {
        $stmt = $this->pdo->query(
            "SELECT n.nspname
             FROM pg_class c
             JOIN pg_namespace n ON n.oid = c.relnamespace
             WHERE c.relname = 'e_notas_otra_region_stgr'
               AND c.relkind = 'r'
               AND n.nspname NOT LIKE 'pg_%'
             ORDER BY n.nspname"
        );
        if ($stmt === false) {
            throw new \RuntimeException(_('No se pudo leer el residuo de notas de otras regiones.'));
        }

        $esquemas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $nombre) {
            if (!is_string($nombre) || preg_match('/^[A-Za-z0-9_-]+$/', $nombre) !== 1) {
                continue;
            }
            $esquemas[] = $nombre;
        }

        return $esquemas;
    }

    private function esquemaDePaso(): ?string
    {
        $stmt = $this->pdo->query(
            "SELECT n.nspname
             FROM pg_class c
             JOIN pg_namespace n ON n.oid = c.relnamespace
             WHERE c.relname = 'p_de_paso_ex'
               AND n.nspname LIKE 'resto%'
             ORDER BY n.nspname
             LIMIT 1"
        );
        if ($stmt === false) {
            return null;
        }
        $nombre = $stmt->fetchColumn();
        if (!is_string($nombre) || preg_match('/^[A-Za-z0-9_-]+$/', $nombre) !== 1) {
            return null;
        }

        return $nombre;
    }

    /**
     * @return array<string, true>
     */
    private function certificadosEnModulo(): array
    {
        $stmt = $this->pdo->query(
            "SELECT DISTINCT certificado FROM e_certificados_emitidos WHERE certificado IS NOT NULL AND certificado <> ''"
        );
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
