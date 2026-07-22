<?php

declare(strict_types=1);

namespace src\notas\application;

use DateTimeImmutable;
use PDO;
use RuntimeException;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
use src\shared\config\ConfigGlobal;

/**
 * Inserta marcadores de fin de ciclo (9998 cuadrienio / 9999 bienio) en la DL
 * que ejecuta la acción:
 *
 * - `tipo_acta` = 1 (acta)
 * - `acta` = sigla de la DL del esquema actual (`ConfigGlobal::mi_dele()`)
 * - `detalle` = «fin bienio» / «fin cuadrienio»
 * - `f_acta` = última fecha de acta tipo 1 de la persona en el tramo
 *   (9999: `id_nivel < 2000`; 9998: resto salvo 9998/9999); fallback: hoy
 *
 * La migración 211200 normaliza filas históricas; los inserts nuevos ya nacen
 * con acta=sigla de la DL examinadora.
 */
final class ActaFinCicloInsert
{
    public const ID_FIN_CUADRIENIO = 9998;
    public const ID_FIN_BIENIO = 9999;

    public function __construct(
        private readonly PDO $pdo,
        private readonly string $tablaNotas,
        private readonly ?string $siglaDl = null,
    ) {
        if (!in_array($this->tablaNotas, ['publicv.e_notas', 'publicf.e_notas'], true)) {
            throw new RuntimeException('ActaFinCicloInsert: tabla de expediente no válida');
        }
    }

    /**
     * @return array{
     *   id_nivel: int,
     *   id_asignatura: int,
     *   acta: string,
     *   detalle: string,
     *   f_acta: string,
     *   id_situacion: int,
     *   tipo_acta: int
     * }
     */
    public function build(int $idNom, int $idAsignaturaFin): array
    {
        if (!in_array($idAsignaturaFin, [self::ID_FIN_CUADRIENIO, self::ID_FIN_BIENIO], true)) {
            throw new RuntimeException('ActaFinCicloInsert: id_asignatura debe ser 9998 o 9999');
        }

        $sigla = trim($this->siglaDl ?? ConfigGlobal::mi_dele());
        if ($sigla === '') {
            throw new RuntimeException(sprintf(
                'ActaFinCicloInsert: sin sigla de DL para id_nom=%d asig=%d',
                $idNom,
                $idAsignaturaFin
            ));
        }

        $detalle = $idAsignaturaFin === self::ID_FIN_BIENIO ? 'fin bienio' : 'fin cuadrienio';
        $fActa = $this->ultimaFActaTipo1($idNom, $idAsignaturaFin);
        if ($fActa === null || $fActa === '') {
            $fActa = (new DateTimeImmutable('today'))->format('Y-m-d');
        }

        return [
            'id_nivel' => $idAsignaturaFin,
            'id_asignatura' => $idAsignaturaFin,
            'acta' => $sigla,
            'detalle' => $detalle,
            'f_acta' => $fActa,
            'id_situacion' => NotaSituacion::SUPERADA,
            'tipo_acta' => TipoActa::FORMATO_ACTA,
        ];
    }

    public function insertIntoDl(int $idNom, int $idAsignaturaFin, string $tablaNotasDl = 'e_notas_dl'): void
    {
        if ($tablaNotasDl !== 'e_notas_dl') {
            throw new RuntimeException('ActaFinCicloInsert: solo se escribe en e_notas_dl local');
        }

        $d = $this->build($idNom, $idAsignaturaFin);
        $sql = "INSERT INTO {$tablaNotasDl}
            (id_nom, id_nivel, id_asignatura, f_acta, id_situacion, acta, detalle, tipo_acta)
            VALUES
            (:id_nom, :id_nivel, :id_asignatura, :f_acta, :id_situacion, :acta, :detalle, :tipo_acta)";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt === false) {
            throw new RuntimeException('ActaFinCicloInsert: prepare falló');
        }
        $stmt->execute([
            'id_nom' => $idNom,
            'id_nivel' => $d['id_nivel'],
            'id_asignatura' => $d['id_asignatura'],
            'f_acta' => $d['f_acta'],
            'id_situacion' => $d['id_situacion'],
            'acta' => $d['acta'],
            'detalle' => $d['detalle'],
            'tipo_acta' => $d['tipo_acta'],
        ]);
    }

    private function ultimaFActaTipo1(int $idNom, int $idAsignaturaFin): ?string
    {
        $filtroNivel = $idAsignaturaFin === self::ID_FIN_BIENIO
            ? 'AND a.id_nivel < 2000'
            : '';

        $sql = "
            SELECT a.f_acta::text AS f_acta
            FROM {$this->tablaNotas} a
            WHERE a.id_nom = :id_nom
              AND a.id_asignatura NOT IN (9998, 9999)
              AND COALESCE(a.tipo_acta, 1) = 1
              {$filtroNivel}
              AND a.f_acta IS NOT NULL
            ORDER BY a.f_acta DESC NULLS LAST, a.id_nivel DESC
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt === false) {
            throw new RuntimeException('ActaFinCicloInsert: prepare f_acta falló');
        }
        $stmt->execute(['id_nom' => $idNom]);
        $fActa = $stmt->fetchColumn();
        if ($fActa === false || $fActa === null || $fActa === '') {
            return null;
        }

        return substr((string) $fActa, 0, 10);
    }
}
