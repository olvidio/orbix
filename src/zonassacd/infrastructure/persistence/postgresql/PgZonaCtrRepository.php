<?php

declare(strict_types=1);

namespace src\zonassacd\infrastructure\persistence\postgresql;

use PDO;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\traits\HandlesPdoErrors;
use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;

class PgZonaCtrRepository extends ClaseRepository implements ZonaCtrRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $this->throwOnError = false;
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('zonas_ctr');
    }

    /**
     * @return list<int>
     */
    public function idUbisDeZona(int $id_zona): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT id_ubi FROM $nom_tabla WHERE id_zona = :id_zona ORDER BY id_ubi";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false || !$this->pdoExecute($stmt, ['id_zona' => $id_zona], __METHOD__, __FILE__, __LINE__)) {
            return [];
        }

        $ids = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row) || !isset($row['id_ubi']) || !is_numeric($row['id_ubi'])) {
                continue;
            }
            $ids[] = (int) $row['id_ubi'];
        }

        return $ids;
    }

    public function zonaDeCentro(int $id_ubi): ?int
    {
        $mapa = $this->mapaZonaPorCentro([$id_ubi]);

        return $mapa[$id_ubi] ?? null;
    }

    /**
     * @param list<int> $idUbis
     * @return array<int, int>
     */
    public function mapaZonaPorCentro(array $idUbis): array
    {
        if ($idUbis === []) {
            return [];
        }

        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $partes = [];
        foreach ($idUbis as $id) {
            $partes[] = (string) (int) $id;
        }
        $sql = "SELECT id_ubi, id_zona FROM $nom_tabla WHERE id_ubi IN (" . implode(',', $partes) . ')';
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $mapa = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row) || !isset($row['id_ubi'], $row['id_zona'])) {
                continue;
            }
            if (!is_numeric($row['id_ubi']) || !is_numeric($row['id_zona'])) {
                continue;
            }
            $mapa[(int) $row['id_ubi']] = (int) $row['id_zona'];
        }

        return $mapa;
    }

    public function asignar(int $id_ubi, ?int $id_zona): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($id_zona === null) {
            $sql = "DELETE FROM $nom_tabla WHERE id_ubi = :id_ubi";

            return $this->pdoExecConParams($oDbl, $sql, ['id_ubi' => $id_ubi]);
        }

        $sql = "INSERT INTO $nom_tabla (id_ubi, id_zona) VALUES (:id_ubi, :id_zona)
                ON CONFLICT (id_ubi) DO UPDATE SET id_zona = EXCLUDED.id_zona";

        return $this->pdoExecConParams($oDbl, $sql, ['id_ubi' => $id_ubi, 'id_zona' => $id_zona]);
    }

    public function eliminarPorZona(int $id_zona): int
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_zona = :id_zona";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false || !$this->pdoExecute($stmt, ['id_zona' => $id_zona], __METHOD__, __FILE__, __LINE__)) {
            return 0;
        }

        return $stmt->rowCount();
    }

    /**
     * @param array<string, int> $params
     */
    private function pdoExecConParams(PDO $oDbl, string $sql, array $params): bool
    {
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        return $this->pdoExecute($stmt, $params, __METHOD__, __FILE__, __LINE__);
    }
}
