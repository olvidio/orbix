<?php

namespace src\dbextern\infrastructure\persistence\postgresql;

use PDO;
use RuntimeException;
use src\dbextern\domain\entity\DlListas;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\traits\HandlesPdoErrors;

class OdbcDlListasRepository extends ClaseRepository
{
    use HandlesPdoErrors;

    public function __construct()
    {
        try {
            $oDbl = GlobalPdo::get('oDBListas');
        } catch (RuntimeException) {
            exit(_("no se puede conectar con la base de datos de Listas"));
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl);
        $this->setNomTabla('dbo.q_Aux_Dl');
    }

    /**
     * Todas las filas de `q_Aux_Dl` (el único llamante no filtra).
     *
     * @return list<DlListas>
     */
    public function getDlListas(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $stmt = $this->prepareAndExecute($oDbl, "SELECT * FROM $nom_tabla", [], __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        /** @var list<DlListas> $dlListas */
        $dlListas = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $dlListas[] = DlListas::fromArray($normalized);
        }

        return $dlListas;
    }

    public function Eliminar(DlListas $DlListas): bool
    {
        $numero_dl = $DlListas->getNumero_dl();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE numero_dl = $numero_dl";

        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(DlListas $DlListas): bool
    {
        $numero_dl = $DlListas->getNumero_dl();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($numero_dl);

        $aDatos = $DlListas->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['nuemro_dl']);
            $update = "
					dl                  = :dl,
					nombre_dl           = :nombre_dl,
					abr_r               = :abr_r,
					numero_r            = :numero_r";
            $sql = "UPDATE $nom_tabla SET $update WHERE nuemro_dl = $numero_dl";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = "(nuemro_dl,dl,nombre_dl,abr_r,numero_r)";
            $valores = "(:nuemro_dl,:dl,:nombre_dl,:abr_r,:numero_r)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }

        if ($stmt === false) {
            return false;
        }

        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $numero_dl): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE numero_dl = $numero_dl";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false || !$stmt->rowCount()) {
            return true;
        }

        return false;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $numero_dl): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE numero_dl = $numero_dl";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }

        return $result;
    }

    public function findById(int $numero_dl): ?DlListas
    {
        $aDatos = $this->datosById($numero_dl);
        if (empty($aDatos)) {
            return null;
        }

        return DlListas::fromArray($aDatos);
    }
}
